@extends('layouts.app')
@section('title', 'Tambah Kriteria')

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('criteria.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0">Tambah Kriteria</h4>
</div>

<div class="card shadow-sm" style="max-width:500px">
    <div class="card-body">
        <form action="{{ route('criteria.store') }}" method="POST">
            @csrf
            @include('criteria._form')
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="{{ route('criteria.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
