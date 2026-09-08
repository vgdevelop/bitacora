<label>{{ $input->label }}{{ $input->required?' *':'' }}@if($input->unit)<small> ({{ $input->unit }})</small>@endif
@switch($input->field_type)
@case('textarea')<textarea name="inputs[{{ $input->id }}]" rows="3" @required($input->required)>{{ $value }}</textarea>@break
@case('number')<input type="number" step="any" name="inputs[{{ $input->id }}]" value="{{ $value }}" @required($input->required)>@break
@case('boolean')<select name="inputs[{{ $input->id }}]" @required($input->required)><option value="">Seleccionar</option><option value="1" @selected((string)$value==='1')>Sí</option><option value="0" @selected((string)$value==='0')>No</option></select>@break
@case('date')<input type="date" name="inputs[{{ $input->id }}]" value="{{ $value }}" @required($input->required)>@break
@case('datetime')<input type="datetime-local" name="inputs[{{ $input->id }}]" value="{{ $value }}" @required($input->required)>@break
@case('select')<select name="inputs[{{ $input->id }}]" @required($input->required)><option value="">Seleccionar</option>@foreach($input->options??[] as $option)<option value="{{ $option }}" @selected($value===$option)>{{ $option }}</option>@endforeach</select>@break
@default<input name="inputs[{{ $input->id }}]" value="{{ $value }}" @required($input->required)>@endswitch
@if($input->help_text)<small class="field-help">{{ $input->help_text }}</small>@endif</label>
