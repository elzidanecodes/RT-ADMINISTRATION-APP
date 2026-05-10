<?php

namespace App\Console\Commands;

use App\Services\BillingService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyBills extends Command
{
    protected $signature = 'bills:generate-monthly
                            {--year= : Period year (default: current year)}
                            {--month= : Period month (default: current month)}';

    protected $description = 'Generate monthly bills for all occupied houses';

    public function handle(BillingService $billingService): int
    {
        $year  = (int) ($this->option('year')  ?: Carbon::now()->year);
        $month = (int) ($this->option('month') ?: Carbon::now()->month);

        $this->info("Generating bills for {$year}-{$month}...");

        $result = $billingService->generateMonthlyBills($year, $month);

        $this->info("✓ Created: {$result['created']} bills");
        $this->line("  Skipped: {$result['skipped']} (already exist)");

        return Command::SUCCESS;
    }
}
