<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Plan::upsert([
            ['name'=>'Basic','price'=>0,'interval'=>'month'],
            ['name'=>'Basic','price'=>500,'interval'=>'month'],
            ['name'=>'Basic','price'=>1000,'interval'=>'month'],
        ], ['name']);
    }
}
