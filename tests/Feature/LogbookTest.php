<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Department;
use App\Models\Location;
use App\Models\TaskInput;
use App\Models\TaskType;
use App\Models\User;
use App\Models\WorkLog;
use App\Models\WorkTeam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LogbookTest extends TestCase
{
    use RefreshDatabase;

    public function test_suite_uses_isolated_sqlite_database(): void
    {
        $this->assertSame('sqlite', DB::connection()->getDriverName());
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/bitacora')->assertRedirect('/login');
    }

    public function test_supervisor_can_record_a_manual_job(): void
    {
        $token = 'test-token';
        $user = User::factory()->create(['role' => 'supervisor', 'is_admin' => true]);
        $department = Department::create(['name' => 'Mantenimiento', 'code' => 'MNT', 'active' => true]);
        $team = WorkTeam::create(['department_id' => $department->id, 'name' => 'Turno A', 'code' => 'MNT-A', 'active' => true]);
        $location = Location::create(['name' => 'Sala 01', 'code' => 'S-01', 'type' => 'room', 'active' => true]);
        $asset = Asset::create(['location_id' => $location->id, 'name' => 'Deshumidificador', 'code' => 'DH-01', 'type' => 'dehumidifier', 'status' => 'operational']);
        $this->withSession(['_token' => $token])->actingAs($user)->post('/admin/bitacora', ['_token' => $token, 'work_team_id' => $team->id, 'location_id' => $location->id, 'asset_id' => $asset->id, 'title' => 'Limpieza de filtro', 'work_type' => 'preventive', 'priority' => 'normal', 'status' => 'completed', 'description' => 'Se retiró y limpió el filtro.', 'started_at' => '2026-09-05 08:00', 'finished_at' => '2026-09-05 08:30'])->assertRedirect();
        $this->assertDatabaseHas('work_logs', ['title' => 'Limpieza de filtro', 'created_by' => $user->id, 'asset_id' => $asset->id]);
    }

    public function test_asset_must_belong_to_selected_location(): void
    {
        $token = 'test-token';
        $user = User::factory()->create(['role' => 'supervisor', 'is_admin' => true]);
        $department = Department::create(['name' => 'Operaciones', 'code' => 'OPE', 'active' => true]);
        $team = WorkTeam::create(['department_id' => $department->id, 'name' => 'Equipo A', 'code' => 'OPE-A', 'active' => true]);
        $one = Location::create(['name' => 'Sala 01', 'code' => 'S-01', 'type' => 'room', 'active' => true]);
        $two = Location::create(['name' => 'Sala 02', 'code' => 'S-02', 'type' => 'room', 'active' => true]);
        $asset = Asset::create(['location_id' => $one->id, 'name' => 'Aire acondicionado', 'code' => 'AA-01', 'type' => 'air_conditioner', 'status' => 'operational']);
        $this->withSession(['_token' => $token])->actingAs($user)->post('/admin/bitacora', ['_token' => $token, 'work_team_id' => $team->id, 'location_id' => $two->id, 'asset_id' => $asset->id, 'title' => 'Control', 'work_type' => 'inspection', 'priority' => 'normal', 'status' => 'completed', 'description' => 'Control visual.', 'started_at' => '2026-09-05 08:00'])->assertStatus(422);
        $this->assertSame(0, WorkLog::count());
    }

    public function test_technician_starts_and_finishes_without_editing_timestamps(): void
    {
        $token = 'test-token';
        $user = User::factory()->create(['role' => 'technician', 'active' => true]);
        $department = Department::create(['name' => 'Servicios', 'code' => 'SER', 'active' => true]);
        $team = WorkTeam::create(['department_id' => $department->id, 'name' => 'Técnicos', 'code' => 'SER-T', 'active' => true]);
        $user->workTeams()->attach($team);
        $location = Location::create(['name' => 'Sala 03', 'code' => 'S-03', 'type' => 'room', 'active' => true]);
        $asset = Asset::create(['location_id' => $location->id, 'name' => 'Aire 3', 'code' => 'AA-03', 'type' => 'air_conditioner', 'status' => 'operational']);
        $type = TaskType::where('code', 'inspection')->firstOrFail();
        $this->withSession(['_token' => $token])->actingAs($user)->post(route('operator.start', $asset), ['_token' => $token, 'task_type_id' => $type->id, 'started_at' => '2000-01-01 00:00'])->assertRedirect();
        $log = WorkLog::firstOrFail();
        $this->assertNotEquals('2000-01-01 00:00:00', $log->started_at->format('Y-m-d H:i:s'));
        $this->withSession(['_token' => $token])->actingAs($user)->put(route('operator.finish', [$asset, $log]), ['_token' => $token, 'result' => 'Operativo', 'finished_at' => '2000-01-01 00:00'])->assertRedirect(route('logbook.show', $log));
        $log->refresh();
        $this->assertSame('completed', $log->status);
        $this->assertNotNull($log->finished_at);
        $this->assertNotEquals('2000-01-01 00:00:00', $log->finished_at->format('Y-m-d H:i:s'));
    }

    public function test_technician_cannot_open_administration_or_manual_edit(): void
    {
        $user = User::factory()->create(['role' => 'technician', 'is_admin' => false]);
        $this->actingAs($user)->get('/admin/planta')->assertForbidden();
        $this->actingAs($user)->get('/admin/bitacora/nuevo')->assertForbidden();
    }

    public function test_configurable_inputs_are_validated_and_snapshotted(): void
    {
        $user = User::factory()->create(['role' => 'technician', 'active' => true]);
        $department = Department::create(['name' => 'Calidad', 'code' => 'CAL', 'active' => true]);
        $team = WorkTeam::create(['department_id' => $department->id, 'name' => 'Calidad A', 'code' => 'CAL-A', 'active' => true]);
        $user->workTeams()->attach($team);
        $location = Location::create(['name' => 'Sala 04', 'code' => 'S-04', 'type' => 'room', 'active' => true]);
        $asset = Asset::create(['location_id' => $location->id, 'name' => 'Sensor', 'code' => 'SE-04', 'type' => 'sensor', 'status' => 'operational']);
        $type = TaskType::create(['code' => 'control-temp', 'name' => 'Control de temperatura', 'peo_code' => 'PEO-CAL-01', 'peo_version' => 'Rev. 2', 'active' => true]);
        $input = TaskInput::create(['task_type_id' => $type->id, 'key' => 'temperatura', 'label' => 'Temperatura', 'field_type' => 'number', 'phase' => 'start', 'unit' => '°C', 'required' => true, 'active' => true]);
        $this->actingAs($user)->post(route('operator.start', $asset), ['task_type_id' => $type->id])->assertSessionHasErrors('inputs.'.$input->id);
        $this->actingAs($user)->post(route('operator.start', $asset), ['task_type_id' => $type->id, 'inputs' => [$input->id => '22.5']])->assertRedirect();
        $log = WorkLog::firstOrFail();
        $input->update(['label' => 'Temperatura nueva', 'unit' => 'K']);
        $this->assertDatabaseHas('work_log_inputs', ['work_log_id' => $log->id, 'input_label' => 'Temperatura', 'unit' => '°C', 'value' => '22.5']);
        $this->assertSame('PEO-CAL-01 · Rev. 2', $log->peo_reference);
    }
}
