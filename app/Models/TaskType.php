<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskType extends Model
{
    protected $fillable = ['code', 'name', 'peo_code', 'peo_title', 'peo_version', 'instructions', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function inputs(): HasMany
    {
        return $this->hasMany(TaskInput::class)->orderBy('sort_order')->orderBy('id');
    }

    public function activeInputs(): HasMany
    {
        return $this->inputs()->where('active', true);
    }

    public function getPeoReferenceAttribute(): ?string
    {
        return $this->peo_code ? trim($this->peo_code.($this->peo_version ? ' · '.$this->peo_version : '')) : null;
    }
}
