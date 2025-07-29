<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Catalog;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        Catalog::create(['name' => 'Toán học']);
        Catalog::create(['name' => 'Khoa học']);
        Catalog::create(['name' => 'Lịch sử']);
    }
}