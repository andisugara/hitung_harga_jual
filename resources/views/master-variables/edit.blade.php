@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h2 class="h4 mb-0">Edit Variable Platform</h2>
            <div class="text-secondary small">{{ $platform->name }} ({{ $platform->slug }})</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('master-variables.show', $platform) }}" class="btn btn-outline-secondary">Kembali</a>
            <button type="submit" form="variableForm" class="btn btn-primary">Simpan</button>
        </div>
    </div>

    <form method="POST" action="{{ route('master-variables.update', $platform) }}" id="variableForm" class="card shadow-sm">
        @csrf
        @method('PUT')

        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between gap-3">
                <div>
                    <h3 class="h5 mb-1">Daftar Variable</h3>
                    <div class="text-secondary small">Atur potongan persen dan/atau potongan fix per transaksi.</div>
                </div>
            </div>

            <hr>

            @php
                $oldRows = old('variables');
                $rows = is_array($oldRows)
                    ? $oldRows
                    : $platform->variables
                        ->map(
                            fn($v) => [
                                'variable' => $v->variable,
                                'type' => $v->type,
                                'value' => $v->value,
                            ],
                        )
                        ->toArray();
                if (count($rows) === 0) {
                    $rows = [['variable' => '', 'type' => 'percent', 'value' => 0]];
                }
            @endphp

            <div class="variable-rows vstack gap-2">
                @foreach ($rows as $index => $row)
                    <div class="row g-2 align-items-end variable-row">
                        <div class="col-12 col-lg-5">
                            <label class="form-label mb-1">Variable</label>
                            <input type="text" name="variables[{{ $index }}][variable]"
                                value="{{ $row['variable'] ?? '' }}" class="form-control" required>
                        </div>

                        <div class="col-12 col-lg-3">
                            <label class="form-label mb-1">Tipe</label>
                            <select name="variables[{{ $index }}][type]" class="form-select" required>
                                <option value="percent" @selected(($row['type'] ?? '') === 'percent')>Percent (%)</option>
                                <option value="amount" @selected(($row['type'] ?? '') === 'amount')>Rupiah (Rp)</option>
                            </select>
                        </div>

                        <div class="col-12 col-lg-3">
                            <label class="form-label mb-1">Value</label>
                            <input type="number" step="0.01" min="0" name="variables[{{ $index }}][value]"
                                value="{{ $row['value'] ?? 0 }}" class="form-control" required>
                        </div>

                        <div class="col-12 col-lg-1 d-grid">
                            <button type="button" class="remove-row btn btn-outline-secondary">Hapus</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <template class="row-template">
                <div class="row g-2 align-items-end variable-row">
                    <div class="col-12 col-lg-5">
                        <label class="form-label mb-1">Variable</label>
                        <input type="text" data-name="variable" class="form-control" required>
                    </div>

                    <div class="col-12 col-lg-3">
                        <label class="form-label mb-1">Tipe</label>
                        <select data-name="type" class="form-select" required>
                            <option value="percent">Percent (%)</option>
                            <option value="amount">Rupiah (Rp)</option>
                        </select>
                    </div>

                    <div class="col-12 col-lg-3">
                        <label class="form-label mb-1">Value</label>
                        <input type="number" step="0.01" min="0" data-name="value" class="form-control" required
                            value="0">
                    </div>

                    <div class="col-12 col-lg-1 d-grid">
                        <button type="button" class="remove-row btn btn-outline-secondary">Hapus</button>
                    </div>
                </div>
            </template>
        </div>

        <div class="card-footer bg-white d-flex align-items-center justify-content-between flex-wrap gap-2">
            <button type="button" class="add-row btn btn-outline-primary">+ Tambah Variable</button>
            <div class="text-secondary small">Tips: total potongan % harus &lt; 100%.</div>
        </div>
    </form>

    <script>
        (function() {
            const form = document.getElementById('variableForm');
            const rowsContainer = form.querySelector('.variable-rows');
            const addButton = form.querySelector('.add-row');
            const template = form.querySelector('.row-template');

            const refreshIndexes = () => {
                rowsContainer.querySelectorAll('.variable-row').forEach((row, index) => {
                    row.querySelectorAll('[data-name]').forEach((field) => {
                        const key = field.getAttribute('data-name');
                        field.setAttribute('name', `variables[${index}][${key}]`);
                    });

                    row.querySelectorAll('input[name], select[name]').forEach((field) => {
                        if (field.hasAttribute('data-name')) {
                            return;
                        }

                        if (field.name.includes('variables[')) {
                            const key = field.name.endsWith('[variable]') ? 'variable' : (field.name
                                .endsWith('[type]') ? 'type' : 'value');
                            field.name = `variables[${index}][${key}]`;
                        }
                    });
                });
            };

            const bindRemove = (row) => {
                row.querySelector('.remove-row')?.addEventListener('click', () => {
                    if (rowsContainer.querySelectorAll('.variable-row').length <= 1) {
                        return;
                    }

                    row.remove();
                    refreshIndexes();
                });
            };

            rowsContainer.querySelectorAll('.variable-row').forEach(bindRemove);

            addButton.addEventListener('click', () => {
                const clone = template.content.firstElementChild.cloneNode(true);
                rowsContainer.appendChild(clone);
                bindRemove(clone);
                refreshIndexes();
            });

            refreshIndexes();
        })();
    </script>
@endsection
