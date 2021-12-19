<?php

namespace Database\Seeders;

use App\Models\Resturant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        

        Role::create(['name' => 'admin']);
        
        Role::create(['name' => 'employer']);


        $admin= User::create([
            'name'=>'admin',
            'email'=>'admin@admin.com',
            'password'=>Hash::make('secret'), 
            'employee_no'=>'1111'
         ]);
 
         
         $admin->assignRole('admin');

         Resturant::create([
             'name'=>'sary',
             'start_shift'=>Carbon::createFromFormat('H:i:s', '08:00:00'),
             'end_shift'=>Carbon::createFromFormat('H:i:s', '23:00:00')
         ]);

    }
}
