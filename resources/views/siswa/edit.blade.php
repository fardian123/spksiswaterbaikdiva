@extends('layouts.app')

@section('title', 'Edit Siswa')

@section('content')
<h4 class="mb-3">Edit Siswa</h4>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('siswa.update', $siswa) }}" method="POST">
            @csrf
            @method('PUT')
            @include('siswa._form')
        </form>
    </div>
</div>
@endsection