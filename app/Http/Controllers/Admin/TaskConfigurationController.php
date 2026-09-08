<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskInput;
use App\Models\TaskType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TaskConfigurationController extends Controller
{
    public function index(): View
    {
        return view('admin.tasks.index', ['taskTypes' => TaskType::with('inputs')->orderBy('name')->get(), 'fieldTypes' => TaskInput::TYPES, 'phases' => TaskInput::PHASES]);
    }

    public function storeType(Request $r): RedirectResponse
    {
        TaskType::create($this->typeData($r) + ['active' => true]);

        return back()->with('success', 'Tipo de tarea creado.');
    }

    public function updateType(Request $r, TaskType $taskType): RedirectResponse
    {
        $taskType->update($this->typeData($r, $taskType));

        return back()->with('success', 'Tipo de tarea actualizado.');
    }

    public function destroyType(TaskType $taskType): RedirectResponse
    {
        $taskType->update(['active' => false]);

        return back()->with('success', 'Tipo de tarea archivado. Su historial se conserva.');
    }

    public function storeInput(Request $r, TaskType $taskType): RedirectResponse
    {
        $taskType->inputs()->create($this->inputData($r, $taskType) + ['active' => true]);

        return back()->with('success', 'Campo agregado al formulario.');
    }

    public function updateInput(Request $r, TaskType $taskType, TaskInput $taskInput): RedirectResponse
    {
        abort_unless($taskInput->task_type_id === $taskType->id, 404);
        $taskInput->update($this->inputData($r, $taskType, $taskInput));

        return back()->with('success', 'Campo actualizado. Los registros anteriores no cambian.');
    }

    public function destroyInput(TaskType $taskType, TaskInput $taskInput): RedirectResponse
    {
        abort_unless($taskInput->task_type_id === $taskType->id, 404);
        $taskInput->update(['active' => false]);

        return back()->with('success', 'Campo archivado. Las respuestas anteriores se conservan.');
    }

    private function typeData(Request $r, ?TaskType $type = null): array
    {
        return $r->validate(['code' => ['required', 'string', 'max:40', Rule::unique('task_types')->ignore($type)], 'name' => 'required|string|max:150', 'peo_code' => 'nullable|string|max:100', 'peo_title' => 'nullable|string|max:255', 'peo_version' => 'nullable|string|max:40', 'instructions' => 'nullable|string|max:5000', 'active' => 'sometimes|boolean']);
    }

    private function inputData(Request $r, TaskType $type, ?TaskInput $input = null): array
    {
        $r->merge(['key' => $r->filled('key') ? $r->string('key')->toString() : Str::slug($r->string('label')->toString(), '_')]);
        $data = $r->validate(['key' => ['required', 'string', 'max:80', Rule::unique('task_inputs')->where('task_type_id', $type->id)->ignore($input)], 'label' => 'required|string|max:180', 'field_type' => ['required', Rule::in(array_keys(TaskInput::TYPES))], 'phase' => ['required', Rule::in(array_keys(TaskInput::PHASES))], 'unit' => 'nullable|string|max:40', 'help_text' => 'nullable|string|max:1000', 'options_text' => 'nullable|string|max:5000', 'required' => 'sometimes|boolean', 'active' => 'sometimes|boolean', 'sort_order' => 'nullable|integer|min:0|max:9999']);
        $options = collect(preg_split('/\r\n|\r|\n/', $data['options_text'] ?? ''))->map(fn ($v) => trim($v))->filter()->values()->all();
        unset($data['options_text']);
        $data['options'] = $options ?: null;
        $data['required'] = $data['required'] ?? false;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
