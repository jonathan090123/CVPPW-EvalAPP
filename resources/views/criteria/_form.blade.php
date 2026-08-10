<div class="mb-3">
    <label class="form-label fw-semibold">Nama Kriteria</label>
    <input type="text" name="name" value="{{ old('name', $criterion->name ?? '') }}"
           class="form-control @error('name') is-invalid @enderror"
           placeholder="Contoh: Kehadiran, Produktivitas">
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label fw-semibold">Jenis</label>
    <select name="type" class="form-select @error('type') is-invalid @enderror">
        <option value="benefit" {{ old('type', $criterion->type ?? 'benefit') === 'benefit' ? 'selected' : '' }}>
            Benefit (semakin besar semakin baik)
        </option>
        <option value="cost" {{ old('type', $criterion->type ?? '') === 'cost' ? 'selected' : '' }}>
            Cost (semakin kecil semakin baik)
        </option>
    </select>
    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<p class="text-muted small mb-0">
    <i class="bi bi-info-circle me-1"></i>Bobot kriteria diatur per jabatan melalui menu Setting Proporsi.
</p>