<?php 

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            'dono',
            'usuario',
            'funcionario',
            'contador',
            'fornecedor',
            'prestador',
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}