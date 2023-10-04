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
        $dateOptions = [
            '2023-10-03',
            '2023-10-01',
            '2023-10-02',
            '2023-10-04'
        ];

        $timeOptions = [
            '09:30',
            '10:00',
            '11:15',
            '14:30',
        ];
       
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => 'User '. $i,
                'email' => 'user'. $i . '@gmail.com',
                'password' => bcrypt('mypassword'),
                'contact_no' => '+1234567890',
            ]);

            for ($j = 1; $j <= 10; $j++) {
                $randomDate = $dateOptions[array_rand($dateOptions)];
                $randomTime = $timeOptions[array_rand($timeOptions)];
                Destination::create([
                    'user_id' => $user->id,
                    'destName' => 'Destination '. $j,
                    'contactNo'=> '+0123456789',
                    'Location' =>'Location' . $j,
                    'scheduled_date' => $randomDate,
                    'scheduled_time' => $randomTime,
                ]);
            }
        }
    }
}

   