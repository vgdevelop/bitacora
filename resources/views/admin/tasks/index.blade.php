@extends('layouts.app')
@section('title','Tareas y PEO')
@section('content')
<div class="page-head"><div><span class="eyebrow">CONFIGURACIÓN OPERATIVA</span><h1>Tareas, PEO e inputs</h1><p>Configura los formularios utilizados por los operarios. El PEO es sólo una referencia al documento físico vigente.</p></div></div>
<details class="create-panel" open><summary>+ Crear tipo de tarea</summary><form method="post" action="{{ route('admin.task-types.store') }}" class="form-grid">@csrf
 <label>Nombre<input name="name" required placeholder="Ej. Limpieza profunda de HVAC"></label><label>Código<input name="code" required placeholder="LIM-HVAC"></label>
 <label>Código del PEO<input name="peo_code" placeholder="PEO-MNT-014"></label><label>Versión/revisión<input name="peo_version" placeholder="Rev. 3"></label>
 <label class="wide">Título del PEO<input name="peo_title"></label><label class="wide">Nota para el operario<textarea name="instructions" rows="2" placeholder="Referencia breve; no sustituye al documento físico"></textarea></label>
 <button class="button primary">Crear tarea</button></form></details>

<div class="task-config-list">
@foreach($taskTypes as $taskType)<section class="admin-section task-config"><div class="section-title"><div><span class="eyebrow">{{ $taskType->code }} @if($taskType->peo_reference) · {{ $taskType->peo_reference }} @endif</span><h2>{{ $taskType->name }}</h2></div><span class="status">{{ $taskType->active?'Activa':'Archivada' }}</span></div>
 <details><summary>Editar datos generales</summary><form method="post" action="{{ route('admin.task-types.update',$taskType) }}" class="form-grid">@csrf @method('PUT')
  <label>Nombre<input name="name" value="{{ $taskType->name }}" required></label><label>Código<input name="code" value="{{ $taskType->code }}" required></label><label>Código PEO<input name="peo_code" value="{{ $taskType->peo_code }}"></label><label>Versión<input name="peo_version" value="{{ $taskType->peo_version }}"></label><label class="wide">Título PEO<input name="peo_title" value="{{ $taskType->peo_title }}"></label><label class="wide">Nota<textarea name="instructions">{{ $taskType->instructions }}</textarea></label><label class="check"><input type="hidden" name="active" value="0"><input type="checkbox" name="active" value="1" @checked($taskType->active)> Activa</label><button class="button primary">Guardar</button>
 </form></details>
 <h3>Inputs del formulario</h3>
 <div class="entity-list">@forelse($taskType->inputs as $input)<details class="entity"><summary><span><strong>{{ $input->label }}</strong><small>{{ $fieldTypes[$input->field_type]??$input->field_type }} · {{ $phases[$input->phase]??$input->phase }}{{ $input->required?' · obligatorio':'' }}</small></span><span>{{ $input->active?'Activo':'Archivado' }}</span></summary>
  <form method="post" action="{{ route('admin.task-inputs.update',[$taskType,$input]) }}" class="form-grid">@csrf @method('PUT') @include('admin.tasks.input-fields',['input'=>$input])<button class="button primary">Guardar campo</button></form>
  @if($input->active)<form method="post" action="{{ route('admin.task-inputs.destroy',[$taskType,$input]) }}">@csrf @method('DELETE')<button class="danger-link">Archivar campo</button></form>@endif
 </details>@empty<p>Aún no tiene campos personalizados.</p>@endforelse</div>
 <details class="create-panel"><summary>+ Agregar input</summary><form method="post" action="{{ route('admin.task-inputs.store',$taskType) }}" class="form-grid">@csrf @include('admin.tasks.input-fields',['input'=>null])<button class="button primary">Agregar campo</button></form></details>
 @if($taskType->active)<form method="post" action="{{ route('admin.task-types.destroy',$taskType) }}" onsubmit="return confirm('¿Archivar este tipo de tarea?')">@csrf @method('DELETE')<button class="danger-link">Archivar tipo de tarea</button></form>@endif
</section>@endforeach
</div>
@endsection
