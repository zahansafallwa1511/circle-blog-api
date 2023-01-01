<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Role;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Article::factory(10)->create();
        $adminRoleId = Role::where('role', 'admin')->first()->id;
        $authorRoleId = Role::where('role', 'author')->first()->id;
        User::first()->roles()->attach($adminRoleId);
        $users = User::all();
        foreach($users as $user){
            $user->roles()->attach($authorRoleId);
        }
    }
}
