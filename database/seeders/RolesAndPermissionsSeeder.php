<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create permissions
        Permission::firstOrCreate(['name' => 'access_asset_booking_menu']);

        // Create roles and assign existing permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo('access_asset_booking_menu');

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->givePermissionTo('access_asset_booking_menu');

        // You might want to assign other existing permissions to these roles as well
        // For example:
        // $adminRole->givePermissionTo('access_asset_menu');
        // $adminRole->givePermissionTo('access_maintenance_menu');
        // etc.
    }
}
