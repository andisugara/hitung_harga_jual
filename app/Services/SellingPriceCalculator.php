<?php

namespace App\Services;

use App\Models\Platform;

class SellingPriceCalculator
{
    public function baseCost(float $buyPrice, float $additionalCost): float
    {
        return $buyPrice + $additionalCost;
    }

    public function targetProfit(float $baseCost, string $marginType, float $marginValue): float
    {
        if ($marginType === 'percent') {
            return $baseCost * ($marginValue / 100);
        }

        return $marginValue;
    }

    public function calculateForPlatform(float $baseCost, float $targetProfit, Platform $platform): array
    {
        $totalDeductionPercent = (float) $platform->variables
            ->where('type', 'percent')
            ->sum('value');

        $fixedFee = (float) $platform->variables
            ->where('type', 'amount')
            ->sum('value');

        if ($totalDeductionPercent >= 100) {
            return [
                'valid' => false,
                'message' => "Total potongan {$platform->name} tidak boleh >= 100%.",
            ];
        }

        $targetRevenueAfterCut = $baseCost + $targetProfit;
        $sellingPrice = ($targetRevenueAfterCut + $fixedFee) / (1 - ($totalDeductionPercent / 100));

        $deductionAmount = $sellingPrice * ($totalDeductionPercent / 100);
        $netProfit = $sellingPrice - $deductionAmount - $fixedFee - $baseCost;

        return [
            'valid' => true,
            'platform_id' => $platform->id,
            'total_deduction_percent' => round($totalDeductionPercent, 2),
            'fixed_fee_amount' => round($fixedFee, 2),
            'selling_price' => round($sellingPrice, 2),
            'net_profit' => round($netProfit, 2),
        ];
    }
}
