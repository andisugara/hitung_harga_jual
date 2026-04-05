@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h4 mb-0">Tambah Master Produk</h2>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('products.store') }}" class="row g-3">
                @csrf

                <div class="col-12">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Harga Beli (Rp)</label>
                    <input type="number" step="0.01" min="0" name="buy_price" value="{{ old('buy_price') }}"
                        class="form-control" required>
                </div>

                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <label class="form-label mb-0">Biaya Tambahan (Dinamis)</label>
                            <div class="text-secondary small">Isi beberapa biaya tambahan (contoh: Packaging, Ongkir, Bubble
                                wrap).</div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="addAdditionalCost">+
                            Tambah</button>
                    </div>

                    @php
                        $rows = old('additional_costs');
                        if (!is_array($rows) || count($rows) === 0) {
                            $rows = [['name' => '', 'value' => 0]];
                        }
                    @endphp

                    <div class="vstack gap-2 mt-2" id="additionalCostRows">
                        @foreach ($rows as $index => $row)
                            <div class="row g-2 align-items-end additional-cost-row">
                                <div class="col-12 col-lg-6">
                                    <label class="form-label mb-1">Name</label>
                                    <input type="text" name="additional_costs[{{ $index }}][name]"
                                        value="{{ $row['name'] ?? '' }}" class="form-control"
                                        placeholder="Contoh: Packaging">
                                </div>

                                <div class="col-12 col-lg-5">
                                    <label class="form-label mb-1">Value</label>
                                    <input type="number" step="0.01" min="0"
                                        name="additional_costs[{{ $index }}][value]" value="{{ $row['value'] ?? 0 }}"
                                        class="form-control">
                                </div>

                                <div class="col-12 col-lg-1 d-grid">
                                    <button type="button"
                                        class="btn btn-outline-secondary remove-additional-cost">Hapus</button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <template id="additionalCostTemplate">
                        <div class="row g-2 align-items-end additional-cost-row">
                            <div class="col-12 col-lg-6">
                                <label class="form-label mb-1">Name</label>
                                <input type="text" data-name="name" class="form-control" placeholder="Contoh: Packaging">
                            </div>

                            <div class="col-12 col-lg-5">
                                <label class="form-label mb-1">Value</label>
                                <input type="number" step="0.01" min="0" data-name="value" class="form-control"
                                    value="0">
                            </div>

                            <div class="col-12 col-lg-1 d-grid">
                                <button type="button"
                                    class="btn btn-outline-secondary remove-additional-cost">Hapus</button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Jenis Margin</label>
                    <select name="margin_type" class="form-select" required>
                        <option value="percent" @selected(old('margin_type') === 'percent')>Persen (%)</option>
                        <option value="amount" @selected(old('margin_type') === 'amount')>Rupiah (Rp)</option>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Nilai Margin</label>
                    <input type="number" step="0.01" min="0" name="margin_value"
                        value="{{ old('margin_value') }}" class="form-control" required>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Hitung & Simpan</button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function() {
            const rows = document.getElementById('additionalCostRows');
            const addBtn = document.getElementById('addAdditionalCost');
            const template = document.getElementById('additionalCostTemplate');

            const refreshIndexes = () => {
                rows.querySelectorAll('.additional-cost-row').forEach((row, index) => {
                    row.querySelectorAll('[data-name]').forEach((field) => {
                        const key = field.getAttribute('data-name');
                        field.setAttribute('name', `additional_costs[${index}][${key}]`);
                    });

                    row.querySelectorAll('input[name]').forEach((field) => {
                        if (field.hasAttribute('data-name')) return;
                        if (!field.name.includes('additional_costs[')) return;

                        const key = field.name.endsWith('[name]') ? 'name' : 'value';
                        field.name = `additional_costs[${index}][${key}]`;
                    });
                });
            };

            const bindRemove = (row) => {
                row.querySelector('.remove-additional-cost')?.addEventListener('click', () => {
                    if (rows.querySelectorAll('.additional-cost-row').length <= 1) {
                        return;
                    }
                    row.remove();
                    refreshIndexes();
                });
            };

            rows.querySelectorAll('.additional-cost-row').forEach(bindRemove);

            addBtn.addEventListener('click', () => {
                const clone = template.content.firstElementChild.cloneNode(true);
                rows.appendChild(clone);
                bindRemove(clone);
                refreshIndexes();
            });

            refreshIndexes();
        })();
    </script>
@endsection
