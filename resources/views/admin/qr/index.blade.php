@extends('layouts.app')
@section('title','Códigos QR')
@section('content')
<div class="page-head no-print"><div><span class="eyebrow">Identificación de activos</span><h1>Códigos QR de dispositivos</h1><p>Imprime y fija cada etiqueta en su equipo. El enlace abre directamente el inicio o cierre de tarea.</p></div><button class="button" onclick="window.print()">Imprimir etiquetas</button></div>
<div class="qr-grid">
@forelse($assets as $asset)<article class="qr-label"><img src="{{ route('admin.qr.image',$asset) }}" alt="QR {{ $asset->code }}"><div><span class="eyebrow">{{ $asset->location->name }}</span><h2>{{ $asset->name }}</h2><strong>{{ $asset->code }}</strong><small>{{ route('operator.asset',$asset) }}</small></div></article>@empty<p>No hay dispositivos. Créelos primero desde Administración.</p>@endforelse
</div>
@endsection
