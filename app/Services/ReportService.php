<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Expense;
use App\Models\House;
use App\Models\Payment;
use App\Models\Resident;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getAnnualChart(int $year): array
    {
        // Aggregate income (payments) and expenses per month in a single pass each
        $incomeByMonth = Payment::selectRaw('MONTH(payment_date) as month, SUM(amount_paid) as total')
            ->whereYear('payment_date', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->map(fn ($v) => (float) $v)
            ->toArray();

        $expenseByMonth = Expense::selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
            ->whereYear('expense_date', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->map(fn ($v) => (float) $v)
            ->toArray();

        $running = 0;
        $monthly = collect(range(1, 12))->map(function (int $month) use ($incomeByMonth, $expenseByMonth, &$running, $year) {
            $inc  = $incomeByMonth[$month]  ?? 0;
            $exp  = $expenseByMonth[$month] ?? 0;
            $running += ($inc - $exp);

            return [
                'month'           => $month,
                'month_name'      => Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F'),
                'income'          => $inc,
                'expense'         => $exp,
                'balance'         => $inc - $exp,
                'running_balance' => $running,
            ];
        });

        return [
            'year'    => $year,
            'summary' => [
                'total_income'  => $monthly->sum('income'),
                'total_expense' => $monthly->sum('expense'),
                'balance'       => $monthly->sum('balance'),
            ],
            'monthly' => $monthly->values()->all(),
        ];
    }

    public function getMonthlyDetail(int $year, int $month): array
    {
        $periodLabel = Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y');

        // Income: payments made in this month
        $payments = Payment::with(['bill.house', 'bill.resident'])
            ->whereYear('payment_date', $year)
            ->whereMonth('payment_date', $month)
            ->get();

        $totalIncome     = $payments->sum(fn ($p) => (float) $p->amount_paid);
        $incomeByType    = $payments->groupBy('bill.bill_type')->map(fn ($g) => $g->sum(fn ($p) => (float) $p->amount_paid));

        $incomeItems = $payments->map(fn ($p) => [
            'id'           => $p->id,
            'bill_type'    => $p->bill?->bill_type,
            'house_number' => $p->bill?->house?->house_number,
            'resident_name'=> $p->bill?->resident?->full_name,
            'amount_paid'  => (float) $p->amount_paid,
            'payment_date' => $p->payment_date?->toDateString(),
        ]);

        // Expenses recorded in this month
        $expenses = Expense::with('category')
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month)
            ->get();

        $totalExpense      = $expenses->sum(fn ($e) => (float) $e->amount);
        $expenseByCategory = $expenses->groupBy('category.name')->map(fn ($g) => [
            'category' => $g->first()?->category?->name,
            'total'    => $g->sum(fn ($e) => (float) $e->amount),
        ])->values();

        $expenseItems = $expenses->map(fn ($e) => [
            'id'           => $e->id,
            'title'        => $e->title,
            'category'     => $e->category?->name,
            'amount'       => (float) $e->amount,
            'expense_date' => $e->expense_date?->toDateString(),
        ]);

        return [
            'period'  => $periodLabel,
            'income'  => [
                'total'    => $totalIncome,
                'by_type'  => [
                    'security' => $incomeByType['security'] ?? 0,
                    'cleaning' => $incomeByType['cleaning'] ?? 0,
                ],
                'payments' => $incomeItems->values()->all(),
            ],
            'expense' => [
                'total'       => $totalExpense,
                'by_category' => $expenseByCategory->all(),
                'items'       => $expenseItems->values()->all(),
            ],
            'balance' => $totalIncome - $totalExpense,
        ];
    }

    public function getDashboardSummary(): array
    {
        $now   = Carbon::now();
        $year  = $now->year;
        $month = $now->month;

        $periodLabel = $now->locale('id')->translatedFormat('F Y');

        $thisMonthIncome  = (float) Payment::whereYear('payment_date', $year)->whereMonth('payment_date', $month)->sum('amount_paid');
        $thisMonthExpense = (float) Expense::whereYear('expense_date', $year)->whereMonth('expense_date', $month)->sum('amount');

        $unpaidBills = Bill::where('status', '!=', 'paid')->get();

        return [
            'current_month'      => $periodLabel,
            'occupied_houses'    => House::where('status', 'occupied')->count(),
            'vacant_houses'      => House::where('status', 'vacant')->count(),
            'total_residents'    => Resident::count(),
            'this_month'         => [
                'income'  => $thisMonthIncome,
                'expense' => $thisMonthExpense,
                'balance' => $thisMonthIncome - $thisMonthExpense,
            ],
            'unpaid_bills_count' => $unpaidBills->count(),
            'unpaid_bills_total' => $unpaidBills->sum(fn ($b) => (float) $b->amount),
        ];
    }
}
