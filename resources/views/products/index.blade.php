@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h2 class="h4 mb-0">Master Produk</h2>
            <div class="text-secondary small">Kelola produk, modal dinamis, dan harga jual per platform.</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('products.export') }}" class="btn btn-outline-success">Export Excel</a>
            <a href="{{ route('products.create') }}" class="btn btn-primary">Tambah Produk</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0 datatable">
                <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th>Harga Beli</th>
                        <th>Biaya Tambahan</th>
                        <th>Target Profit</th>
                        <th>Harga Jual Platform</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td class="fw-semibold">{{ $product->name }}</td>
                            <td>Rp {{ number_format($product->buy_price, 2, ',', '.') }}</td>
                            <td>Rp {{ number_format($product->additional_cost, 2, ',', '.') }}</td>
                            <td>Rp {{ number_format($product->target_profit, 2, ',', '.') }}</td>
                            <td>
                                <ul class="mb-0 ps-3">
                                    @foreach ($product->platformPrices as $row)
                                        <li>{{ $row->platform->name }}: Rp
                                            {{ number_format($row->selling_price, 2, ',', '.') }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('products.show', $product) }}"
                                        class="btn btn-sm btn-outline-primary">Detail</a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}"
                                        onsubmit="return confirm('Hapus produk ini? Harga per platform ikut terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
