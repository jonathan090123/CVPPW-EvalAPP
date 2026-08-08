<div class="mb-3">
    <label class="form-label fw-semibold">NIP</label>
    <input type="text" name="nip" value="{{ old('nip', $employee->nip ?? '') }}"
           class="form-control @error('nip') is-invalid @enderror"
           placeholder="Nomor Induk Pegawai">
    @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label fw-semibold">Nama Lengkap</label>
    <input type="text" name="name" value="{{ old('name', $employee->name ?? '') }}"
           class="form-control @error('name') is-invalid @enderror"
           placeholder="Nama lengkap karyawan">
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label fw-semibold">Departemen</label>
    <select name="department" class="form-select @error('department') is-invalid @enderror" required>
        <option value="" disabled {{ old('department', $employee->department ?? '') ? '' : 'selected' }}>-- Pilih Departemen --</option>
        @php
            $departments = ['PEMBELIAN', 'MARKETING', 'FINANCE'];
        @endphp
        @foreach ($departments as $departmentOption)
            <option value="{{ $departmentOption }}" {{ old('department', $employee->department ?? '') === $departmentOption ? 'selected' : '' }}>
                {{ $departmentOption }}
            </option>
        @endforeach
    </select>
    @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label fw-semibold">Jabatan</label>
    <select name="position" class="form-select @error('position') is-invalid @enderror" required>
        <option value="" disabled {{ old('position', $employee->position ?? '') ? '' : 'selected' }}>-- Pilih Jabatan --</option>
        @php
            $positions = ['KEPALA BAGIAN', 'KOORDINATOR', 'STAFF'];
        @endphp
        @foreach ($positions as $positionOption)
            <option value="{{ $positionOption }}" {{ old('position', $employee->position ?? '') === $positionOption ? 'selected' : '' }}>
                {{ $positionOption }}
            </option>
        @endforeach
    </select>
    @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
