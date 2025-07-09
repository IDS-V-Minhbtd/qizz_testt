<?php

use App\Models\Catalog;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        Catalog::create(['name' => 'Toán học', 'description' => 'Quiz về Toán']);
        Catalog::create(['name' => 'Khoa học', 'description' => 'Quiz về Khoa học']);
        Catalog::create(['name' => 'Lịch sử', 'description' => 'Quiz về Lịch sử']);
    }
}
