<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Gaji Satpam',       'frequency' => 'monthly',    'description' => 'Gaji bulanan petugas keamanan'],
            ['name' => 'Token Listrik',      'frequency' => 'monthly',    'description' => 'Biaya listrik fasilitas umum'],
            ['name' => 'Perbaikan Jalan',    'frequency' => 'occasional', 'description' => 'Biaya perbaikan jalan lingkungan'],
            ['name' => 'Perbaikan Selokan',  'frequency' => 'occasional', 'description' => 'Biaya perbaikan saluran air'],
            ['name' => 'Lainnya',            'frequency' => 'occasional', 'description' => 'Pengeluaran lain-lain'],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
