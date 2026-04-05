@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h2 class="h4 mb-0">Detail Produk</h2>
            <div class="text-secondary">{{ $product->name }}</div>
        </div>
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('products.destroy', $product) }}"
                onsubmit="return confirm('Hapus produk ini? Harga per platform ikut terhapus.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Hapus</button>
            </form>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <h3 class="h5 mb-3">{{ $product->name }}</h3>
            <div class="row g-2">
                <div class="col-12 col-md-6">Harga Beli: <span class="fw-semibold">Rp
                        {{ number_format($product->buy_price, 2, ',', '.') }}</span></div>
                <div class="col-12 col-md-6">Biaya Tambahan: <span class="fw-semibold">Rp
                        {{ number_format($product->additional_cost, 2, ',', '.') }}</span></div>
                @if ($product->additionalCostItems->count() > 0)
                    <div class="col-12">
                        <div class="small text-secondary">Rincian biaya tambahan:</div>
                        <ul class="small mb-0">
                            @foreach ($product->additionalCostItems as $item)
                                <li>{{ $item->name }}: Rp {{ number_format($item->value, 2, ',', '.') }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="col-12 col-md-6">Total Modal: <span class="fw-semibold">Rp
                        {{ number_format($product->base_cost, 2, ',', '.') }}</span></div>
                <div class="col-12 col-md-6">Target Profit: <span class="fw-semibold">Rp
                        {{ number_format($product->target_profit, 2, ',', '.') }}</span></div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Platform</th>
                        <th>Total Potongan (%)</th>
                        <th>Potongan Fix (Rp)</th>
                        <th>Harga Jual</th>
                        <th>Estimasi Profit Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($product->platformPrices as $row)
                        <tr>
                            <td class="fw-semibold">
                                {{ $row->platform->name }}
                                <div class="small text-secondary mt-1">
                                    @if ($row->platform->variables->count() > 0)
                                        {{ $row->platform->variables->map(function ($v) {
                                                $val =
                                                    $v->type === 'percent'
                                                        ? rtrim(rtrim(number_format($v->value, 2, '.', ''), '0'), '.') . '%'
                                                        : 'Rp ' . number_format($v->value, 2, ',', '.');
                                                return $v->variable . ' (' . $val . ')';
                                            })->implode(', ') }}
                                    @else
                                        (Tidak ada variable)
                                    @endif
                                </div>
                            </td>
                            <td>{{ number_format($row->total_deduction_percent, 2, ',', '.') }}%</td>
                            <td>Rp {{ number_format($row->fixed_fee_amount, 2, ',', '.') }}</td>
                            <td class="fw-semibold">Rp {{ number_format($row->selling_price, 2, ',', '.') }}</td>
                            <td>Rp {{ number_format($row->net_profit, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
