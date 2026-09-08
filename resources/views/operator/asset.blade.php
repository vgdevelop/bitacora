@extends('layouts.app')
@section('title','Trabajo en '.$asset->name)
@section('content')
<section class="scan-card">
 <div class="scan-context"><span class="eyebrow">{{ $asset->location->type_label }} · {{ $asset->location->name }}</span><h1>{{ $asset->name }}</h1><p>{{ $asset->code }} · {{ $asset->type }} · <strong>{{ $asset->status_label }}</strong></p></div>
 @if(!$active)
 <div class="action-state ready"><span class="state-dot"></span>Sin tarea tuya en curso</div>
 <form method="post" action="{{ route('operator.start',$asset) }}" class="operator-form" id="start-task">@csrf
  <label>¿Qué vas a hacer?<select name="task_type_id" required autofocus id="task-type"><option value="">Selecciona el tipo de trabajo</option>@foreach($taskTypes as $type)<option value="{{ $type->id }}" @selected(old('task_type_id')==$type->id)>{{ $type->name }}</option>@endforeach</select></label>
  @foreach($taskTypes as $type)<div class="task-fields" data-task="{{ $type->id }}" hidden>@if($type->peo_reference)<div class="peo-reference"><strong>PEO: {{ $type->peo_reference }}</strong>@if($type->peo_title)<span>{{ $type->peo_title }}</span>@endif @if($type->instructions)<p>{{ $type->instructions }}</p>@endif</div>@endif @foreach($type->inputs->filter(fn($i)=>in_array($i->phase,['start','both'])) as $input)@include('operator.input',['input'=>$input,'value'=>old('inputs.'.$input->id)])@endforeach</div>@endforeach
  <details class="optional"><summary>Agregar una nota opcional</summary><label>Título breve<input name="title" value="{{ old('title') }}" maxlength="255"></label><label>Descripción inicial<textarea name="description" rows="3">{{ old('description') }}</textarea></label></details>
  <button class="primary-action" type="submit">Iniciar tarea ahora</button><p class="auto-time">La hora de inicio se registra automáticamente.</p>
 </form>
 @else
 <div class="action-state working"><span class="state-dot"></span>Tarea en curso desde {{ $active->started_at->format('H:i') }}</div>
 <div class="active-summary"><strong>{{ $active->type_label }}</strong><span>{{ $active->number }}</span></div>
 <form method="post" action="{{ route('operator.finish',[$asset,$active]) }}" class="operator-form">@csrf @method('PUT')
  @if($active->peo_reference)<div class="peo-reference"><strong>PEO: {{ $active->peo_reference }}</strong><span>{{ $active->taskType?->peo_title }}</span></div>@endif
  <h2>Completar el trabajo</h2><p>Agrega la información útil. Los campos marcados con * son obligatorios.</p>
  @foreach(($active->taskType?->inputs??collect())->where('active',true)->filter(fn($i)=>in_array($i->phase,['finish','both'])) as $input)@include('operator.input',['input'=>$input,'value'=>old('inputs.'.$input->id)])@endforeach
  <label>Título breve<input name="title" value="{{ old('title',$active->title) }}" maxlength="255"></label><label>Trabajo realizado<textarea name="description" rows="3">{{ old('description',$active->description) }}</textarea></label><label>Resultado<textarea name="result" rows="3">{{ old('result',$active->result) }}</textarea></label><label>Observaciones<textarea name="observations" rows="3">{{ old('observations',$active->observations) }}</textarea></label>
  <label>Prioridad<select name="priority">@foreach(\App\Models\WorkLog::PRIORITIES as $value=>$label)<option value="{{ $value }}" @selected(old('priority',$active->priority)===$value)>{{ $label }}</option>@endforeach</select></label><label>Próxima acción sugerida<input type="datetime-local" name="next_action_at" value="{{ old('next_action_at') }}"></label>
  <button class="primary-action finish" type="submit">Finalizar tarea ahora</button><p class="auto-time">La hora final se registra automáticamente.</p>
 </form>
 @endif
</section>
@if(!$active)<script>const select=document.getElementById('task-type');function showTask(){document.querySelectorAll('.task-fields').forEach(group=>{const on=group.dataset.task===select.value;group.hidden=!on;group.querySelectorAll('input,select,textarea').forEach(el=>el.disabled=!on)})}select.addEventListener('change',showTask);showTask()</script>@endif
@endsection
