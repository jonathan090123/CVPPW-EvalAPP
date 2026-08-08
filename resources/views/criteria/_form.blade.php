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
@php
    $othersTotal = isset($criterion)
        ? \App\Models\Criterion::where('id', '!=', $criterion->id)->sum('weight')
        : \App\Models\Criterion::sum('weight');
    $remaining = max(0, 100 - $othersTotal);
@endphp
<div class="mb-3">
    <label class="form-label fw-semibold">
        Bobot (%)
        <small class="text-muted">
            (0 – 100, total semua bobot harus = 100%).
            Sisa bobot tersedia: <strong>{{ number_format($remaining, 2) }}%</strong>
        </small>
    </label>
    <input type="number" name="weight" step="0.01" min="0.01" max="{{ $remaining }}"
           value="{{ old('weight', $criterion->weight ?? '') }}"
           class="form-control @error('weight') is-invalid @enderror"
           placeholder="Contoh: 20"
           oninvalid="this.setCustomValidity('Nilai tidak boleh melebihi sisa bobot tersedia ({{ number_format($remaining, 2) }}%).')"
           oninput="this.setCustomValidity('')">
    @error('weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>