<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => '人事部'],
            ['name' => '営業部'],
            ['name' => '総務部'],
            ['name' => '開発部'],
            ['name' => '経理部'],
        ];
        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
