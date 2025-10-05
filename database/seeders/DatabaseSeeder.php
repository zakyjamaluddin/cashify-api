<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['id' => (string) Str::uuid(), 'name' => 'Gaji', 'type' => 'Income', 'icon' => 'circle-stack', 'color' => '#3B82F6', 'is_default' => true, 'created_at' => now()],
            ['id' => (string) Str::uuid(), 'name' => 'Investasi', 'type' => 'Income', 'icon' => 'banknotes', 'color' => '#3B82F6', 'is_default' => true, 'created_at' => now()],
            ['id' => (string) Str::uuid(), 'name' => 'Bonus', 'type' => 'Income', 'icon' => 'gift', 'color' => '#3B82F6', 'is_default' => true, 'created_at' => now()],
            ['id' => (string) Str::uuid(), 'name' => 'Makanan', 'type' => 'Expense', 'icon' => 'cake', 'color' => '#3B82F6', 'is_default' => true, 'created_at' => now()],
            ['id' => (string) Str::uuid(), 'name' => 'Transportasi', 'type' => 'Expense', 'icon' => 'truck', 'color' => '#3B82F6', 'is_default' => true, 'created_at' => now()],
            ['id' => (string) Str::uuid(), 'name' => 'Hiburan', 'type' => 'Expense', 'icon' => 'music-note', 'color' => '#3B82F6', 'is_default' => true, 'created_at' => now()],
        ]);
    }
}
