<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schedule::insert(
            [
                [
                    'schedule' => 'Morning Schedule',
                    'time' => '08:00 AM - 9:00 AM',
                    'is_enabled' => true,
                ],
                [
                    'schedule' => 'Morning Schedule',
                    'time' => '09:00 AM - 10:00 AM',
                    'is_enabled' => true,
                ],
                [
                    'schedule' => 'Morning Schedule',
                    'time' => '10:00 AM - 11:00 AM',
                    'is_enabled' => true,
                ],
                [
                    'schedule' => 'Morning Schedule',
                    'time' => '11:00 AM - 12:00 PM',
                    'is_enabled' => true,
                ],
                [
                    'schedule' => 'Afternoon Schedule',
                    'time' => '12:00 PM - 1:00 PM',
                    'is_enabled' => true,
                ],
                [
                    'schedule' => 'Afternoon Schedule',
                    'time' => '1:00 PM - 2:00 PM',
                    'is_enabled' => true,
                ],
                [
                    'schedule' => 'Afternoon Schedule',
                    'time' => '2:00 PM - 3:00 PM',
                    'is_enabled' => true,
                ],
                [
                    'schedule' => 'Afternoon Schedule',
                    'time' => '3:00 PM - 4:00 PM',
                    'is_enabled' => true,
                ],
                [
                    'schedule' => 'Afternoon Schedule',
                    'time' => '4:00 PM - 5:00 PM',
                    'is_enabled' => true,
                ],
                [
                    'schedule' => 'Afternoon Schedule',
                    'time' => '5:00 PM - 6:00 PM',
                    'is_enabled' => true,
                ],
                [
                    'schedule' => 'Afternoon Schedule',
                    'time' => '6:00 PM - 7:00 PM',
                    'is_enabled' => true,
                ],
            ]

        );
    }
}
