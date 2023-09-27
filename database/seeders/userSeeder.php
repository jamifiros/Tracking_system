<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Destination;



class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
       
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@gmail.com',
                'password' => bcrypt('mypassword'),

            ]);

            for ($j = 1; $j <= 5; $j++) {
                Destination::create([
                    'user_id' => $user->id,
                    'destName' => 'Destination ' . $j,
                    'contactNo'=> '+0123456789',
                    'Location' =>'Location' . $j
                ]);
            }
        }
    }
}

   