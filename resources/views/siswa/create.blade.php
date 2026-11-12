@extends('layouts.app')

@section('title', 'Tambah Siswa')

@section('content')
<h4 class="mb-3">Tambah Siswa</h4>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('siswa.store') }}" method="POST">
            @csrf
            @include('siswa._form')
        </form>
    </div>
</div>
@endsection