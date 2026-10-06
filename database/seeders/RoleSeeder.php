<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Member', 'slug' => 'member', 'description' => 'Approved branch member'],
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Operational administrator'],
            ['name' => 'Super Admin', 'slug' => 'super_admin', 'description' => 'Primary system administrator'],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
