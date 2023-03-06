<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{
    public function run()
    {
        $company = [
            [
                'id'             => 1,
                'name'           => 'TEST',
            ],
           
        ];

        Company::insert($company);
    }
}
