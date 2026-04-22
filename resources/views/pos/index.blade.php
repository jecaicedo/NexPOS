@extends('layouts.app')
@section('title', 'Punto de Venta')
@section('content')
<div class="h-[calc(100vh-5rem)]">
    @livewire('pos.pos-terminal')
</div>
@endsection
