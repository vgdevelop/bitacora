<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\TaskType;
use App\Models\WorkLog;
use App\Models\WorkTeam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssetWorkController extends Controller
{
    public function show(Request $request, Asset $asset): View
    {
        $active = WorkLog::with(['team', 'author', 'taskType.inputs', 'inputValues'])->where('asset_id', $asset->id)->where('created_by', $request->user()->id)->where('status', 'in_progress')->latest('started_at')->first();

        return view('operator.asset', ['asset' => $asset->load('location'), 'active' => $active, 'taskTypes' => TaskType::with(['inputs' => fn ($q) => $q->where('active', true)->orderBy('sort_order')])->where('active', true)->orderBy('name')->get()]);
    }

    public function start(Request $request, Asset $asset): RedirectResponse
    {
        $data = $request->validate(['task_type_id' => ['required', 'exists:task_types,id'], 'title' => ['nullable', 'string', 'max:255'], 'description' => ['nullable', 'string', 'max:5000']]);
        $taskType = TaskType::with('inputs')->where('active', true)->findOrFail($data['task_type_id']);
        $inputData = $request->validate($this->inputRules($taskType, 'start'));
        $team = $request->user()->workTeams()->where('work_teams.active', true)->first();
        if (! $team && $request->user()->isSupervisor()) {
            $team = WorkTeam::where('active', true)->first();
        }abort_unless($team, 422, 'El usuario no tiene un equipo de trabajo asignado.');
        $exists = WorkLog::where('created_by', $request->user()->id)->where('asset_id', $asset->id)->where('status', 'in_progress')->exists();
        abort_if($exists, 422, 'Ya tienes una tarea activa sobre este equipo.');
        $log = WorkLog::create(['number' => 'BIT-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)), 'created_by' => $request->user()->id, 'work_team_id' => $team->id, 'location_id' => $asset->location_id, 'asset_id' => $asset->id, 'title' => $data['title'] ?? null, 'task_type_id' => $taskType->id, 'task_type_name' => $taskType->name, 'peo_reference' => $taskType->peo_reference, 'work_type' => $taskType->code, 'priority' => 'normal', 'status' => 'in_progress', 'description' => $data['description'] ?? null, 'started_at' => now()]);
        $this->saveInputs($log, $taskType, $inputData, 'start', $request->user()->id);

        return back()->with('success', 'Tarea iniciada. El horario fue registrado automáticamente.');
    }

    public function finish(Request $request, Asset $asset, WorkLog $workLog): RedirectResponse
    {
        abort_unless($workLog->asset_id === $asset->id && ($workLog->created_by === $request->user()->id || $request->user()->isSupervisor()), 403);
        abort_unless($workLog->status === 'in_progress', 422);
        $data = $request->validate(['title' => ['nullable', 'string', 'max:255'], 'description' => ['nullable', 'string', 'max:5000'], 'result' => ['nullable', 'string', 'max:5000'], 'observations' => ['nullable', 'string', 'max:5000'], 'priority' => ['nullable', Rule::in(array_keys(WorkLog::PRIORITIES))], 'next_action_at' => ['nullable', 'date']]);
        $taskType = $workLog->taskType()->with('inputs')->first();
        $inputData = $taskType ? $request->validate($this->inputRules($taskType, 'finish')) : [];
        if ($taskType) {
            $this->saveInputs($workLog, $taskType, $inputData, 'finish', $request->user()->id);
        }
        $workLog->update($data + ['status' => 'completed', 'finished_at' => now(), 'closed_by' => $request->user()->id]);

        return redirect()->route('logbook.show', $workLog)->with('success', 'Tarea finalizada. El horario de cierre fue registrado automáticamente.');
    }

    private function inputRules(TaskType $type, string $phase): array
    {
        $rules = [];
        foreach ($type->inputs->where('active', true)->filter(fn ($i) => in_array($i->phase, [$phase, 'both'])) as $input) {
            $rule = $input->required ? 'required' : 'nullable';
            $extra = match ($input->field_type) {
                'number' => 'numeric','date','datetime' => 'date','boolean' => 'boolean','select' => Rule::in($input->options ?? []),default => 'string|max:5000'
            };
            $rules['inputs.'.$input->id] = [$rule, $extra];
        }

return $rules;
    }

    private function saveInputs(WorkLog $log, TaskType $type, array $data, string $phase, int $userId): void
    {
        foreach ($type->inputs->where('active', true)->filter(fn ($i) => in_array($i->phase, [$phase, 'both'])) as $input) {
            $value = data_get($data, 'inputs.'.$input->id);
            if ($value === null || $value === '') {
                continue;
            }$log->inputValues()->updateOrCreate(['input_key' => $input->key, 'phase' => $phase], ['task_input_id' => $input->id, 'input_label' => $input->label, 'field_type' => $input->field_type, 'unit' => $input->unit, 'value' => is_array($value) ? json_encode($value) : $value, 'recorded_by' => $userId, 'recorded_at' => now()]);
        }
    }
}
