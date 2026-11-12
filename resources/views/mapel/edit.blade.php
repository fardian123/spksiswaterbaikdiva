@extends('layouts.app')

@section('title', 'Edit Mapel')

@section('content')
<h4 class="mb-3">Edit Mapel</h4>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('mapel.update', $mapel) }}" method="POST">
            @csrf
            @method('PUT')
            @include('mapel._form')
        </form>
    </div>
</div>
@endsection