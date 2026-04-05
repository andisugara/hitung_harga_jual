@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h2 class="h4 mb-0">Variable Platform</h2>
            <div class="text-secondary small">{{ $platform->name }} ({{ $platform->slug }})</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('master-variables.index') }}" class="btn btn-outline-secondary">Kembali</a>
            <a href="{{ route('master-variables.edit', $platform) }}" class="btn btn-primary">Edit Variables</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0 datatable">
                    <thead class="table-light">
                        <tr>
                            <th>Variable</th>
                            <th>Tipe</th>
                            <th class="text-end">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($platform->variables as $var)
                            <tr>
                                <td class="fw-semibold">{{ $var->variable }}</td>
                                <td>
                                    @if ($var->type === 'percent')
                                        <span class="badge text-bg-info">Percent</span>
                                    @else
                                        <span class="badge text-bg-warning">Rupiah</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($var->type === 'percent')
                                        {{ rtrim(rtrim(number_format($var->value, 2, '.', ''), '0'), '.') }}%
                                    @else
                                        Rp {{ number_format($var->value, 2, ',', '.') }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-secondary py-5">Belum ada variable.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
