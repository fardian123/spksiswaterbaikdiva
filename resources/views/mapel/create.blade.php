@extends('layouts.app')

@section('title', 'Tambah Mapel')

@section('content')
<h4 class="mb-3">Tambah Mapel</h4>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('mapel.store') }}" method="POST">
            @csrf
            @include('mapel._form')
        </form>
    </div>
</div>
@endsection