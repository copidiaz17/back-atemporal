<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserType;

class DatabaseSeeder extends Seeder
{

    /**
     * 
     
     * Seed the application's database.
     */
    public function run(): void
    {


        UserType::create(['nombre' => 'admin']);
        UserType::create(['nombre' => 'cliente']);
        
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
