@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h2 class="h4 mb-0">Master Variable</h2>
            <div class="text-secondary small">Pilih platform untuk melihat / edit variable (potongan).</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('platforms.index') }}" class="btn btn-outline-secondary">Master Platform</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0 datatable">
                    <thead class="table-light">
                        <tr>
                            <th>Platform</th>
                            <th>Slug</th>
                            <th class="text-end">Jumlah Variable</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($platforms as $platform)
                            <tr>
                                <td class="fw-semibold">{{ $platform->name }}</td>
                                <td><span class="badge text-bg-light">{{ $platform->slug }}</span></td>
                                <td class="text-end">{{ $platform->variables_count }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a class="btn btn-sm btn-outline-primary"
                                            href="{{ route('master-variables.show', $platform) }}">Lihat</a>
                                        <a class="btn btn-sm btn-primary"
                                            href="{{ route('master-variables.edit', $platform) }}">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
