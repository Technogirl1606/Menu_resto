<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Menu du jour',
            'Menu du soir',
            'Boissons',
            'Fastfood',
            'Glacier',
        ];
 
        foreach ($categories as $position => $name) {
            Category::firstOrCreate(
                ['slug' => str($name)->slug()],
                ['name' => $name, 'position' => $position, 'is_active' => true]
            );
        }
    
    }
}
