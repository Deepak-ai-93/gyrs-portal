<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\DB;

class AiMonthlySpend
{
    /**
     * @return array{month: string, reserved_usd: float}|null
     */
    public function reserve(): ?array
    {
        $month = now()->format('Y-m');
        $cap = max(0, (float) config('ai.monthly_budget_usd', 20));
        $inputTokens = (((int) config('ai.max_input_chars', 16000) * 4) + 12000) * 3;
        $outputTokens = max(256, (int) config('ai.max_output_tokens', 1200)) * 3;
        $reservation = $this->cost($inputTokens, $outputTokens);

        return DB::transaction(function () use ($month, $cap, $reservation): ?array {
            DB::table('ai_monthly_spend')->insertOrIgnore([
                'month' => $month,
                'spent_usd' => 0,
                'reserved_usd' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $usage = DB::table('ai_monthly_spend')
                ->where('month', $month)
                ->lockForUpdate()
                ->first();

            if ((float) $usage->spent_usd + (float) $usage->reserved_usd + $reservation > $cap) {
                return null;
            }

            DB::table('ai_monthly_spend')
                ->where('month', $month)
                ->increment('reserved_usd', $reservation, ['updated_at' => now()]);

            return ['month' => $month, 'reserved_usd' => $reservation];
        });
    }

    /**
     * @param  array{month: string, reserved_usd: float}  $reservation
     */
    public function settle(array $reservation, int $inputTokens, int $outputTokens): void
    {
        $actualCost = $this->cost($inputTokens, $outputTokens);

        DB::transaction(function () use ($reservation, $actualCost): void {
            $usage = DB::table('ai_monthly_spend')
                ->where('month', $reservation['month'])
                ->lockForUpdate()
                ->first();

            DB::table('ai_monthly_spend')
                ->where('month', $reservation['month'])
                ->update([
                    'reserved_usd' => max(0, (float) $usage->reserved_usd - $reservation['reserved_usd']),
                    'spent_usd' => (float) $usage->spent_usd + $actualCost,
                    'updated_at' => now(),
                ]);
        });
    }

    private function cost(int $inputTokens, int $outputTokens): float
    {
        $input = ($inputTokens / 1_000_000) * (float) config('ai.input_cost_per_million', 0);
        $output = ($outputTokens / 1_000_000) * (float) config('ai.output_cost_per_million', 0);

        return round($input + $output, 6);
    }
}
