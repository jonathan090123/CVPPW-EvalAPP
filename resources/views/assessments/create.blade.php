@extends('layouts.app')
@section('title', 'Buat Penilaian')

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('assessments.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0">Buat Penilaian Baru</h4>
</div>

@if ($employees->isEmpty() || $criteria->isEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-1"></i>
        @if ($employees->isEmpty())
            Belum ada karyawan. <a href="#" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">Tambah karyawan</a> terlebih dahulu.
        @elseif ($criteria->isEmpty())
            Belum ada kriteria. <a href="{{ route('criteria.create') }}">Tambah kriteria</a> terlebih dahulu.
        @endif
    </div>
@else
<form action="{{ route('assessments.store') }}" method="POST" id="assessmentForm">
    @csrf
    <div class="card shadow-sm mb-4" style="max-width:600px">
        <div class="card-header bg-white fw-semibold">Informasi Penilaian</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Penilaian</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="Contoh: Penilaian Accounting">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Periode</label>
                <input type="text" name="period" value="{{ old('period') }}"
                       class="form-control @error('period') is-invalid @enderror"
                       placeholder="Contoh: Januari - Maret 2026">
                @error('period')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi <small class="text-muted">(opsional)</small></label>
                <textarea name="description" rows="2"
                          class="form-control @error('description') is-invalid @enderror"
                          placeholder="Keterangan tambahan...">{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span><i class="bi bi-people me-1"></i>Pilih Karyawan yang Dinilai <small class="text-muted">(Skala 1–5)</small></span>
            <span class="badge bg-primary fs-6" id="selectionCounter">0 dipilih</span>
        </div>
        <div class="card-body">
            {{-- Filter bar --}}
            <div class="row g-2 mb-3 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" class="form-control"
                               placeholder="Cari nama / NIP...">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select id="filterDept" class="form-select form-select-sm">
                        <option value="">Semua Departemen</option>
                        @foreach ($departments as $d)
                            <option value="{{ $d }}">{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <select id="filterPosition" class="form-select form-select-sm">
                        <option value="">Semua Jabatan</option>
                        @foreach ($positions as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex justify-content-md-end">
                    <button type="button" class="btn btn-sm btn-success w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Manual
                    </button>
                </div>
            </div>

            {{-- Tabel karyawan --}}
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0" id="employeeTable">
                    <thead class="table-dark">
                        <tr>
                            <th class="py-2 px-2 text-center" style="width:40px">
                                <input type="checkbox" id="checkAllHeader" title="Pilih semua / kosongkan semua">
                            </th>
                            <th class="py-2 px-3">Karyawan</th>
                            @foreach ($criteria as $criterion)
                                <th class="py-2 px-2 text-center" style="min-width:100px">
                                    {{ $criterion->name }}
                                    <br>
                                    <span class="badge {{ $criterion->type === 'benefit' ? 'badge-benefit' : 'badge-cost' }} small">
                                        {{ $criterion->type }}
                                    </span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                            <tr class="employee-row"
                                data-id="{{ $employee->id }}"
                                data-name="{{ strtolower($employee->name) }}"
                                data-nip="{{ strtolower($employee->nip) }}"
                                data-dept="{{ $employee->department ?? '' }}"
                                data-position="{{ $employee->position ?? '' }}">
                                <td class="py-2 px-2 text-center align-middle">
                                    <input type="checkbox" name="selected_employees[]"
                                           value="{{ $employee->id }}"
                                           class="form-check-input emp-check"
                                           checked>
                                </td>
                                <td class="py-2 px-3 fw-semibold align-middle">
                                    {{ $employee->name }}
                                    @if ($employee->position)
                                        @positionBadge($employee->position)
                                    @endif
                                    <br><small class="text-muted">{{ $employee->nip }} · {{ $employee->department ?? '-' }}</small>
                                </td>
                                @foreach ($criteria as $criterion)
                                    <td class="p-1 text-center align-middle">
                                        <select name="scores[{{ $employee->id }}][{{ $criterion->id }}]"
                                                class="form-select form-select-sm @error("scores.{$employee->id}.{$criterion->id}") is-invalid @enderror"
                                                style="min-width:70px">
                                            @for ($v = 1; $v <= 5; $v++)
                                                <option value="{{ $v }}" {{ old("scores.{$employee->id}.{$criterion->id}") == $v ? 'selected' : '' }}>{{ $v }}</option>
                                            @endfor
                                        </select>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="text-muted small mt-2" id="emptyNotice" style="display:none">
                Tidak ada karyawan yang cocok dengan filter.
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" form="assessmentForm" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Penilaian
                </button>
                <a href="{{ route('assessments.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>
@endif

{{-- Modal Tambah Karyawan Manual --}}
<div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="quickAddEmployeeForm">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-1"></i>Tambah Karyawan Manual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div id="quickAlert" class="alert alert-danger d-none"></div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">NIP</label>
                        <input type="text" name="nip" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Departemen</label>
                        <input type="text" name="department" class="form-control" list="deptList" required>
                        <datalist id="deptList">
                            @foreach ($departments as $d)
                                <option value="{{ $d }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jabatan</label>
                        <input type="text" name="position" class="form-control" list="posList" required>
                        <datalist id="posList">
                            @foreach ($positions as $p)
                                <option value="{{ $p }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="quickSubmitBtn">
                        <i class="bi bi-save me-1"></i>Simpan & Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
(function () {
    var searchInput = document.getElementById('searchInput');
    var filterDept = document.getElementById('filterDept');
    var filterPosition = document.getElementById('filterPosition');
    var table = document.getElementById('employeeTable');
    var tbody = table ? table.querySelector('tbody') : null;
    var rows = tbody ? Array.from(tbody.querySelectorAll('tr.employee-row')) : [];
    var emptyNotice = document.getElementById('emptyNotice');
    var counter = document.getElementById('selectionCounter');
    var headerCheck = document.getElementById('checkAllHeader');

    function visibleRows() { return rows.filter(r => r.style.display !== 'none'); }

    function applyFilter() {
        var q = (searchInput.value || '').toLowerCase().trim();
        var d = filterDept.value;
        var p = filterPosition.value;
        var anyVisible = false;
        rows.forEach(function (row) {
            var match = (!q || row.dataset.name.includes(q) || row.dataset.nip.includes(q))
                && (d === '' || row.dataset.dept === d)
                && (p === '' || row.dataset.position === p);
            row.style.display = match ? '' : 'none';
            if (match) anyVisible = true;
        });
        emptyNotice.style.display = anyVisible ? 'none' : '';
        updateCounter();
        syncHeaderCheck();
    }

    function updateCounter() {
        var total = rows.filter(function (r) { return r.querySelector('.emp-check').checked; }).length;
        if (counter) counter.textContent = total + ' dipilih';
    }

    function syncHeaderCheck() {
        var vis = visibleRows();
        headerCheck.checked = vis.length > 0 && vis.every(function (r) { return r.querySelector('.emp-check').checked; });
    }

    if (searchInput) searchInput.addEventListener('input', applyFilter);
    if (filterDept) filterDept.addEventListener('change', applyFilter);
    if (filterPosition) filterPosition.addEventListener('change', applyFilter);

    if (headerCheck) headerCheck.addEventListener('change', function () {
        visibleRows().forEach(function (r) { r.querySelector('.emp-check').checked = headerCheck.checked; });
        updateCounter();
    });

    tbody.addEventListener('change', function (e) {
        if (e.target.classList.contains('emp-check')) { updateCounter(); syncHeaderCheck(); }
    });
    updateCounter();

    {{-- AJAX: tambah karyawan manual --}}
    var quickForm = document.getElementById('quickAddEmployeeForm');
    var quickAlert = document.getElementById('quickAlert');
    var quickBtn = document.getElementById('quickSubmitBtn');
    quickForm.addEventListener('submit', function (e) {
        e.preventDefault();
        quickAlert.classList.add('d-none');
        quickAlert.innerHTML = '';
        var data = new FormData(quickForm);
        quickBtn.disabled = true;
        quickBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';
        fetch('{{ route("assessments.storeEmployee") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: data
        }).then(function (res) {
            return res.json().then(function (j) { return { ok: res.ok, body: j }; });
        }).then(function (out) {
            if (!out.ok) {
                var errs = out.body.errors || {};
                var msgs = [];
                Object.keys(errs).forEach(function (k) { msgs.push(errs[k].join(' ')); });
                quickAlert.innerHTML = msgs.join('<br>') || 'Gagal menambah karyawan.';
                quickAlert.classList.remove('d-none');
                quickBtn.disabled = false;
                quickBtn.innerHTML = '<i class="bi bi-save me-1"></i>Simpan & Tambah';
                return;
            }
            var emp = out.body.employee;
            var critList = out.body.criteria;
            addEmployeeRow(emp, critList);
            quickForm.reset();
            var modal = bootstrap.Modal.getInstance(document.getElementById('addEmployeeModal'));
            modal.hide();
            quickBtn.disabled = false;
            quickBtn.innerHTML = '<i class="bi bi-save me-1"></i>Simpan & Tambah';
        }).catch(function () {
            quickAlert.textContent = 'Terjadi kesalahan jaringan.';
            quickAlert.classList.remove('d-none');
            quickBtn.disabled = false;
            quickBtn.innerHTML = '<i class="bi bi-save me-1"></i>Simpan & Tambah';
        });
    });

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c];
        });
    }

    function addEmployeeRow(emp, critList) {
        // hindari duplikat
        if (tbody.querySelector('tr[data-id="' + emp.id + '"]')) {
            applyFilter(); return;
        }
        var tr = document.createElement('tr');
        tr.className = 'employee-row';
        tr.dataset.id = emp.id;
        tr.dataset.name = emp.name.toLowerCase();
        tr.dataset.nip = emp.nip.toLowerCase();
        tr.dataset.dept = emp.department || '';
        tr.dataset.position = emp.position || '';
        var badgePos = emp.position ? window.positionBadgeHtml(emp.position) : '';
        var html = '<td class="py-2 px-2 text-center align-middle">'
            + '<input type="checkbox" name="selected_employees[]" value="' + emp.id + '" class="form-check-input emp-check" checked>'
            + '</td>';
        html += '<td class="py-2 px-3 fw-semibold align-middle">'
            + escapeHtml(emp.name) + badgePos
            + '<br><small class="text-muted">' + escapeHtml(emp.nip) + ' · ' + escapeHtml(emp.department || '-') + '</small>'
            + '</td>';
        critList.forEach(function (c) {
            var opts = '';
            for (var v = 1; v <= 5; v++) opts += '<option value="' + v + '">' + v + '</option>';
            html += '<td class="p-1 text-center align-middle">'
                + '<select name="scores[' + emp.id + '][' + c.id + ']" class="form-select form-select-sm" style="min-width:70px">'
                + opts + '</select></td>';
        });
        tr.innerHTML = html;
        tbody.appendChild(tr);
        rows.push(tr);

        // tambahkan nilai dept/position baru ke dropdown filter jika belum ada
        addToFilter(filterDept, emp.department);
        addToFilter(filterPosition, emp.position);
        applyFilter();
    }

    function addToFilter(select, value) {
        if (!value) return;
        var exists = Array.from(select.options).some(function (o) { return o.value === value; });
        if (!exists) {
            var opt = document.createElement('option');
            opt.value = value; opt.textContent = value;
            select.appendChild(opt);
        }
    }
})();
</script>
@endsection