@extends('layouts.app')
@section('title','Trabajo en '.$asset->name)
@section('content')
<section class="scan-card">
 <div class="scan-context"><span class="eyebrow">{{ $asset->location->type_label }} · {{ $asset->location->name }}</span><h1>{{ $asset->name }}</h1><p>{{ $asset->code }} · {{ $asset->type }} · <strong>{{ $asset->status_label }}</strong></p></div>
 @if(!$active)
 <div class="action-state ready"><span class="state-dot"></span>Sin tarea tuya en curso</div>
 <form method="post" action="{{ route('operator.start',$asset) }}" class="operator-form">@csrf
  <label>¿Qué vas a hacer?<select name="work_type" required autofocus><option value="">Selecciona el tipo de trabajo</option>@foreach($types as $value=>$label)<option value="{{ $value }}" @selected(old('work_type')===$value)>{{ $label }}</option>@endforeach</select></label>
  <details class="optional"><summary>Agregar una nota opcional</summary><label>Título breve<input name="title" value="{{ old('title') }}" maxlength="255"></label><label>Descripción inicial<textarea name="description" rows="3">{{ old('description') }}</textarea></label></details>
  <button class="primary-action" type="submit">Iniciar tarea ahora</button><p class="auto-time">La hora de inicio se registra automáticamente.</p>
 </form>
 @else
 <div class="action-state working"><span class="state-dot"></span>Tarea en curso desde {{ $active->started_at->format('H:i') }}</div>
 <div class="active-summary"><strong>{{ $active->type_label }}</strong><span>{{ $active->number }}</span></div>
 <form method="post" action="{{ route('operator.finish',[$asset,$active]) }}" class="operator-form">@csrf @method('PUT')
  <h2>Completar el trabajo</h2><p>Agrega la información útil. Estos datos son importantes para la bitácora, pero son opcionales.</p>
  <label>Título breve<input name="title" value="{{ old('title',$active->title) }}" maxlength="255" placeholder="Ej. Limpieza de filtros"></label>
  <label>Trabajo realizado<textarea name="description" rows="3" placeholder="Describe lo que hiciste">{{ old('description',$active->description) }}</textarea></label>
  <label>Resultado<textarea name="result" rows="3" placeholder="Estado en que quedó el equipo">{{ old('result',$active->result) }}</textarea></label>
  <label>Observaciones<textarea name="observations" rows="3" placeholder="Hallazgos, repuestos o riesgos">{{ old('observations',$active->observations) }}</textarea></label>
  <label>Prioridad<select name="priority">@foreach(\App\Models\WorkLog::PRIORITIES as $value=>$label)<option value="{{ $value }}" @selected(old('priority',$active->priority)===$value)>{{ $label }}</option>@endforeach</select></label>
  <label>Próxima acción sugerida<input type="datetime-local" name="next_action_at" value="{{ old('next_action_at') }}"></label>
  <button class="primary-action finish" type="submit">Finalizar tarea ahora</button><p class="auto-time">La hora final se registra automáticamente y no puede ser modificada por el operario.</p>
 </form>
 @endif
</section>
@endsection
