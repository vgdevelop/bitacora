<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Department;
use App\Models\Location;
use App\Models\User;
use App\Models\WorkTeam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlantManagementController extends Controller
{
    public function index(): View
    {
        return view('admin.plant.index', ['departments' => Department::withCount('teams')->orderBy('name')->get(), 'teams' => WorkTeam::with('department')->withCount('users')->orderBy('name')->get(), 'locations' => Location::withCount('assets')->orderBy('name')->get(), 'assets' => Asset::with('location')->orderBy('name')->get(), 'users' => User::with('workTeams')->orderBy('name')->get()]);
    }

    public function storeDepartment(Request $r): RedirectResponse
    {
        Department::create($r->validate(['name' => 'required|string|max:120', 'code' => 'required|string|max:20|unique:departments', 'description' => 'nullable|string|max:1000']) + ['active' => true]);

        return back()->with('success', 'Departamento creado.');
    }

    public function updateDepartment(Request $r, Department $department): RedirectResponse
    {
        $department->update($r->validate(['name' => 'required|string|max:120', 'code' => ['required', 'string', 'max:20', Rule::unique('departments')->ignore($department)], 'description' => 'nullable|string|max:1000', 'active' => 'boolean']));

        return back()->with('success', 'Departamento actualizado.');
    }

    public function destroyDepartment(Department $department): RedirectResponse
    {
        return $this->deleteSafely($department, 'Departamento eliminado.');
    }

    public function storeTeam(Request $r): RedirectResponse
    {
        WorkTeam::create($r->validate(['department_id' => 'required|exists:departments,id', 'name' => 'required|string|max:120', 'code' => 'required|string|max:30|unique:work_teams', 'shift' => 'nullable|string|max:30']) + ['active' => true]);

        return back()->with('success', 'Equipo creado.');
    }

    public function updateTeam(Request $r, WorkTeam $team): RedirectResponse
    {
        $team->update($r->validate(['department_id' => 'required|exists:departments,id', 'name' => 'required|string|max:120', 'code' => ['required', 'string', 'max:30', Rule::unique('work_teams')->ignore($team)], 'shift' => 'nullable|string|max:30', 'active' => 'boolean']));

        return back()->with('success', 'Equipo actualizado.');
    }

    public function destroyTeam(WorkTeam $team): RedirectResponse
    {
        return $this->deleteSafely($team, 'Equipo eliminado.');
    }

    public function storeLocation(Request $r): RedirectResponse
    {
        Location::create($r->validate(['name' => 'required|string|max:150', 'code' => 'required|string|max:30|unique:locations', 'type' => ['required', Rule::in(array_keys(Location::TYPES))], 'zone' => 'nullable|string|max:120', 'description' => 'nullable|string|max:1000']) + ['active' => true]);

        return back()->with('success', 'Ubicación creada.');
    }

    public function updateLocation(Request $r, Location $location): RedirectResponse
    {
        $location->update($r->validate(['name' => 'required|string|max:150', 'code' => ['required', 'string', 'max:30', Rule::unique('locations')->ignore($location)], 'type' => ['required', Rule::in(array_keys(Location::TYPES))], 'zone' => 'nullable|string|max:120', 'description' => 'nullable|string|max:1000', 'active' => 'boolean']));

        return back()->with('success', 'Ubicación actualizada.');
    }

    public function destroyLocation(Location $location): RedirectResponse
    {
        return $this->deleteSafely($location, 'Ubicación eliminada.');
    }

    public function storeAsset(Request $r): RedirectResponse
    {
        Asset::create($this->assetData($r));

        return back()->with('success', 'Dispositivo creado.');
    }

    public function updateAsset(Request $r, Asset $asset): RedirectResponse
    {
        $asset->update($this->assetData($r, $asset));

        return back()->with('success', 'Dispositivo actualizado.');
    }

    public function destroyAsset(Asset $asset): RedirectResponse
    {
        return $this->deleteSafely($asset, 'Dispositivo eliminado.');
    }

    public function storeUser(Request $r): RedirectResponse
    {
        $d = $r->validate(['name' => 'required|string|max:120', 'email' => 'required|email|unique:users', 'password' => 'required|string|min:10', 'role' => ['required', Rule::in(['technician', 'supervisor'])], 'team_ids' => 'array', 'team_ids.*' => 'exists:work_teams,id']);
        $u = User::create(['name' => $d['name'], 'email' => $d['email'], 'password' => Hash::make($d['password']), 'role' => $d['role'], 'is_admin' => $d['role'] === 'supervisor', 'active' => true]);
        $u->workTeams()->sync($d['team_ids'] ?? []);

        return back()->with('success', 'Cuenta creada.');
    }

    public function updateUser(Request $r, User $user): RedirectResponse
    {
        $d = $r->validate(['name' => 'required|string|max:120', 'email' => ['required', 'email', Rule::unique('users')->ignore($user)], 'password' => 'nullable|string|min:10', 'role' => ['required', Rule::in(['technician', 'supervisor'])], 'active' => 'boolean', 'team_ids' => 'array', 'team_ids.*' => 'exists:work_teams,id']);
        $user->update(['name' => $d['name'], 'email' => $d['email'], 'role' => $d['role'], 'is_admin' => $d['role'] === 'supervisor', 'active' => $d['active'] ?? false] + (! empty($d['password']) ? ['password' => Hash::make($d['password'])] : []));
        $user->workTeams()->sync($d['team_ids'] ?? []);

        return back()->with('success', 'Cuenta actualizada.');
    }

    public function destroyUser(Request $r, User $user): RedirectResponse
    {
        abort_if($r->user()->is($user), 422, 'No puedes eliminar tu propia cuenta.');

        return $this->deleteSafely($user, 'Cuenta eliminada.');
    }

    private function assetData(Request $r, ?Asset $asset = null): array
    {
        return $r->validate(['location_id' => 'required|exists:locations,id', 'name' => 'required|string|max:150', 'code' => ['required', 'string', 'max:50', Rule::unique('assets')->ignore($asset)], 'type' => 'required|string|max:50', 'brand' => 'nullable|string|max:100', 'model' => 'nullable|string|max:100', 'serial_number' => ['nullable', 'string', 'max:120', Rule::unique('assets')->ignore($asset)], 'status' => ['required', Rule::in(array_keys(Asset::STATUSES))], 'notes' => 'nullable|string|max:2000']);
    }

    private function deleteSafely(Model $model, string $message): RedirectResponse
    {
        try {
            $model->delete();

            return back()->with('success', $message);
        } catch (QueryException) {
            return back()->withErrors('No se puede eliminar porque tiene registros relacionados. Puedes marcarlo como inactivo.');
        }
    }
}
