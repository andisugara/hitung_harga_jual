<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use App\Models\Product;
use App\Services\SellingPriceCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    public function __construct(private readonly SellingPriceCalculator $calculator) {}

    public function index(): View
    {
        return view('products.index', [
            'products' => Product::query()
                ->with(['platformPrices.platform', 'additionalCostItems'])
                ->latest()
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'additional_costs' => ['nullable', 'array'],
            'additional_costs.*.name' => ['nullable', 'string', 'max:255'],
            'additional_costs.*.value' => ['nullable', 'numeric', 'min:0'],
            'margin_type' => ['required', 'in:percent,amount'],
            'margin_value' => ['required', 'numeric', 'min:0'],
        ]);

        $buyPrice = (float) $validated['buy_price'];

        $additionalCostRows = collect($validated['additional_costs'] ?? [])
            ->map(fn($row) => [
                'name' => trim((string) ($row['name'] ?? '')),
                'value' => (float) ($row['value'] ?? 0),
            ])
            ->filter(fn($row) => $row['name'] !== '' || $row['value'] > 0)
            ->values();

        $invalidRowIndex = $additionalCostRows
            ->search(fn($row) => $row['name'] === '' && $row['value'] > 0);

        if ($invalidRowIndex !== false) {
            return back()->withInput()->withErrors([
                'additional_costs' => 'Nama biaya tambahan wajib diisi jika value > 0.',
            ]);
        }

        $additionalCost = (float) $additionalCostRows->sum('value');
        $marginType = $validated['margin_type'];
        $marginValue = (float) $validated['margin_value'];

        $baseCost = $this->calculator->baseCost($buyPrice, $additionalCost);
        $targetProfit = $this->calculator->targetProfit($baseCost, $marginType, $marginValue);

        $platforms = Platform::query()->with('variables')->orderBy('id')->get();
        if ($platforms->isEmpty()) {
            return back()->withInput()->withErrors([
                'platform' => 'Master variable belum tersedia.',
            ]);
        }

        $platformResults = [];
        foreach ($platforms as $platform) {
            $result = $this->calculator->calculateForPlatform($baseCost, $targetProfit, $platform);
            if (! $result['valid']) {
                return back()->withInput()->withErrors([
                    'platform' => $result['message'],
                ]);
            }

            $platformResults[] = $result;
        }

        $product = DB::transaction(function () use ($validated, $baseCost, $targetProfit, $platformResults) {
            $product = Product::query()->create([
                'name' => $validated['name'],
                'buy_price' => $validated['buy_price'],
                'additional_cost' => collect($validated['additional_costs'] ?? [])->sum(fn($row) => (float) ($row['value'] ?? 0)),
                'margin_type' => $validated['margin_type'],
                'margin_value' => $validated['margin_value'],
                'base_cost' => round($baseCost, 2),
                'target_profit' => round($targetProfit, 2),
            ]);

            $additionalCostRows = collect($validated['additional_costs'] ?? [])
                ->map(fn($row) => [
                    'name' => trim((string) ($row['name'] ?? '')),
                    'value' => (float) ($row['value'] ?? 0),
                ])
                ->filter(fn($row) => $row['name'] !== '' || $row['value'] > 0)
                ->filter(fn($row) => $row['name'] !== '')
                ->values();

            if ($additionalCostRows->isNotEmpty()) {
                $product->additionalCostItems()->createMany($additionalCostRows->all());
            }

            $product->platformPrices()->createMany($platformResults);

            return $product;
        });

        return redirect()->route('products.show', $product)->with('success', 'Produk berhasil disimpan sebagai master product.');
    }

    public function show(Product $product): View
    {
        return view('products.show', [
            'product' => $product->load(['platformPrices.platform.variables', 'additionalCostItems']),
        ]);
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function export(): StreamedResponse
    {
        $platforms = Platform::query()->orderBy('id')->get();
        $products = Product::query()
            ->with(['additionalCostItems', 'platformPrices.platform'])
            ->latest()
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="master-produk.csv"',
        ];

        return response()->streamDownload(function () use ($products, $platforms) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            $baseHeader = [
                'ID',
                'Nama Produk',
                'Harga Beli',
                'Biaya Tambahan (Total)',
                'Biaya Tambahan (Detail)',
                'Total Modal',
                'Target Profit',
            ];

            $platformHeaders = $platforms
                ->map(fn($p) => 'Harga Jual - ' . $p->name)
                ->all();

            fputcsv($out, array_merge($baseHeader, $platformHeaders));

            foreach ($products as $product) {
                $detail = $product->additionalCostItems
                    ->map(fn($i) => $i->name . ':' . $i->value)
                    ->implode('; ');

                $row = [
                    $product->id,
                    $product->name,
                    (string) $product->buy_price,
                    (string) $product->additional_cost,
                    $detail,
                    (string) $product->base_cost,
                    (string) $product->target_profit,
                ];

                foreach ($platforms as $platform) {
                    $price = $product->platformPrices
                        ->firstWhere('platform_id', $platform->id)
                        ?->selling_price;
                    $row[] = $price === null ? '' : (string) $price;
                }

                fputcsv($out, $row);
            }

            fclose($out);
        }, 'master-produk.csv', $headers);
    }
}
