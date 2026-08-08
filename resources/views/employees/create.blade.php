@extends('layouts.app')
@section('title', 'Tambah Karyawan')

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0">Tambah Karyawan</h4>
</div>

<div class="card shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf
            @include('employees._form')
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
