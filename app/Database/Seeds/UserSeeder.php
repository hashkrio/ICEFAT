<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = auth()->getProvider();
        
        $user = new \CodeIgniter\Shield\Entities\User([
            'username' => 'liquidfly',
            'email'    => 'liquidfly@example.com',
            'password' => 'Dx88*Z.7z=n}',
            'first_name' => 'Super',
            'last_name' => 'Admin'
        ]);
        
        $users->save($user);

        $user = $users->findById($users->getInsertID());

        $user->syncGroups('superadmin');
    }
}
