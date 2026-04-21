<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'client.create',
            'client.view',
            'client.update',
            'client.delete',
            'invoice.create',
            'invoice.view',
            'invoice.update',
            'invoice.delete',
            'invoice.send',
            'payment.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $accountant = Role::firstOrCreate(['name' => 'accountant']);
        $viewer = Role::firstOrCreate(['name' => 'viewer']);

        $admin->syncPermissions($permissions);

        $accountant->syncPermissions([
            'client.create',
            'client.view',
            'client.update',
            'invoice.create',
            'invoice.view',
            'invoice.update',
            'invoice.send',
            'payment.view',
        ]);

        $viewer->syncPermissions([
            'client.view',
            'invoice.view',
            'payment.view',
        ]);
    }
}
