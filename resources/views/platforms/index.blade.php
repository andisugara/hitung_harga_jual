@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h2 class="h4 mb-0">Master Platform</h2>
            <div class="text-secondary small">Tambah platform baru (Shopee/TikTok/Pribadi/dll).</div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="POST" action="{{ route('platforms.store') }}" class="row g-2 align-items-end">
                @csrf
                <div class="col-12 col-md-6">
                    <label class="form-label">Nama Platform</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control"
                        placeholder="Contoh: Tokopedia" required>
                </div>
                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Tambah Platform</button>
                </div>
                <div class="col-12 col-md-3">
                    <a href="{{ route('master-variables.index') }}" class="btn btn-outline-secondary w-100">Kelola
                        Variables</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0 datatable">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
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
                                    <a href="{{ route('master-variables.show', $platform) }}"
                                        class="btn btn-sm btn-outline-primary">Lihat Variables</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
