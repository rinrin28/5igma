<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = CarbonImmutable::now()->toDateTimeString();

        $users = [
            [
                'name' => 'test0',
                'email' => 'test0@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'department_id' => null,
                'client_company_id' => '1',
                // admin: アンケートを送付する日程を決める管理者用アカウント
            ],
            [
                'name' => 'test1',
                'email' => 'test1@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'executive',
                'department_id' => null,
                'client_company_id' => '1',
                // executive: 経営層用アカウント
            ],
            [
                'name' => 'test2',
                'email' => 'test2@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'management',
                'department_id' => '1',
                'client_company_id' => '1',
                // management: 管理職用アカウント
            ],
            [
                'name' => 'test3',
                'email' => 'test3@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'department_id' => '1',
                'client_company_id' => '1',
                // employee: 従業員用アカウント
            ],
            [
                'name' => '金堂克芳',
                'email' => 'katsuyoshikanado@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'management',
                'department_id' => 1,
                'client_company_id' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => '大渕紫帆',
                'email' => 'shiho.svtc.17@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'management',
                'department_id' => 2,
                'client_company_id' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => '後藤めい',
                'email' => 'meii.04.nico@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'management',
                'department_id' => 3,
                'client_company_id' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => '宍倉りんたろう',
                'email' => 'shishi.rin11313@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'management',
                'department_id' => 4,
                'client_company_id' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => '児玉佳映',
                'email' => 'kae.kodama249@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'management',
                'department_id' => 5,
                'client_company_id' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ];
        foreach ($users as $user) {
            User::create($user);
        }
        User::factory(20)->create();

    }
}
