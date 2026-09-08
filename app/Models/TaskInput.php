<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskInput extends Model
{
    public const TYPES = ['text' => 'Texto corto', 'textarea' => 'Texto largo', 'number' => 'Número', 'boolean' => 'Sí / No', 'date' => 'Fecha', 'datetime' => 'Fecha y hora', 'select' => 'Lista de opciones'];

    public const PHASES = ['start' => 'Al iniciar', 'finish' => 'Al finalizar', 'both' => 'Inicio y finalización'];

    protected $fillable = ['task_type_id', 'key', 'label', 'field_type', 'phase', 'unit', 'help_text', 'options', 'required', 'active', 'sort_order'];

    protected function casts(): array
    {
        return ['options' => 'array', 'required' => 'boolean', 'active' => 'boolean'];
    }

    public function taskType(): BelongsTo
    {
        return $this->belongsTo(TaskType::class);
    }
}
