@extends('layouts.app')
@section('title', 'Setting Proporsi Penilaian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-tuning-knob me-2"></i>Setting Proporsi Penilaian per Jabatan</h4>
    <span class="badge bg-danger" style="font-size:.7rem">
        <i class="bi bi-lock me-1"></i>Hanya Owner
    </span>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i><strong>Gagal menyimpan. Periksa kembali:</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif



<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('proportions.update') }}" method="POST">
            @csrf @method('PUT')

            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="py-2 px-3" style="min-width:160px">Jabatan</th>
                            @foreach ($criteria as $criterion)
                                <th class="py-2 px-2 text-center" style="min-width:110px">
                                    {{ $criterion->name }}
                                    <br>
                                    <span class="badge {{ $criterion->type === 'benefit' ? 'badge-benefit' : 'badge-cost' }} small">
                                        {{ $criterion->type }}
                                    </span>
                                    <br>
                                    <small class="text-light">%</small>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($positions as $pos)
                            @php
                                $totalWeight = 0;
                                foreach ($criteria as $c) {
                                    $totalWeight += (int) round($map[$pos][$c->id]);
                                }
                            @endphp
                            <tr>
                                <td class="py-2 px-3 fw-semibold">
                                    @positionBadge($pos)
                                    <br>
                                    <small class="text-muted">Total bobot: <strong class="js-total-weight">{{ $totalWeight }}%</strong></small>
                                    <br>
                                    <small class="js-total-status {{ $totalWeight === 100 ? 'text-success' : 'text-danger' }}">
                                        {{ $totalWeight === 100 ? 'Sesuai 100%' : 'Belum 100%' }}
                                    </small>
                                </td>
                                @foreach ($criteria as $criterion)
                                    <td class="p-1 text-center">
                                        <input type="number" step="1" min="0" max="100"
                                               inputmode="numeric" pattern="[0-9]*"
                                               name="prop[{{ $pos }}][{{ $criterion->id }}]"
                                               value="{{ (int) round($map[$pos][$criterion->id]) }}"
                                               class="form-control form-control-sm text-center js-prop-input"
                                               style="min-width:80px"
                                               placeholder="10%">
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Setting Bobot
                </button>
                <a href="{{ route('proportions.edit') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Form
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        function updateRowTotal(row) {
            var total = 0;
            row.querySelectorAll('.js-prop-input').forEach(function (input) {
                total += parseInt(input.value, 10) || 0;
            });
            var totalEl = row.querySelector('.js-total-weight');
            if (totalEl) totalEl.textContent = total + '%';
            var statusEl = row.querySelector('.js-total-status');
            if (statusEl) {
                if (total === 100) {
                    statusEl.classList.remove('text-danger');
                    statusEl.classList.add('text-success');
                    statusEl.textContent = 'Sesuai 100%';
                } else {
                    statusEl.classList.remove('text-success');
                    statusEl.classList.add('text-danger');
                    statusEl.textContent = 'Belum 100%';
                }
            }
        }

        document.querySelectorAll('.js-prop-input').forEach(function (input) {
            // Blokir karakter desimal/eksponen agar murni integer (tanpa koma/titik)
            input.addEventListener('keydown', function (e) {
                if (['.', ',', 'e', 'E', '+', '-'].indexOf(e.key) !== -1) {
                    e.preventDefault();
                }
            });
            // Sanitasi hasil ketik/paste: hanya digit, clamp 0-100, lalu update total live
            input.addEventListener('input', function () {
                var digits = input.value.replace(/[^0-9]/g, '');
                if (digits === '') {
                    input.value = '';
                } else {
                    var n = parseInt(digits, 10);
                    if (n > 100) n = 100;
                    input.value = String(n);
                }
                var row = input.closest('tr');
                if (row) updateRowTotal(row);
            });
        });
    })();
</script>
@endpush