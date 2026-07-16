<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = [
            ['name' => 'Grand Theft Auto V', 'steam_app_id' => '271590', 'price' => 350000],
            ['name' => 'Cyberpunk 2077', 'steam_app_id' => '1091500', 'price' => 750000],
            ['name' => 'ELDEN RING', 'steam_app_id' => '1245620', 'price' => 800000],
            ['name' => 'Red Dead Redemption 2', 'steam_app_id' => '1174180', 'price' => 600000],
            ['name' => 'Baldur\'s Gate 3', 'steam_app_id' => '1086940', 'price' => 990000],
            ['name' => 'Hogwarts Legacy', 'steam_app_id' => '990080', 'price' => 850000],
            ['name' => 'Black Myth: Wukong', 'steam_app_id' => '2358720', 'price' => 1299000],
            ['name' => 'Palworld', 'steam_app_id' => '1623730', 'price' => 385000],
        ];

        foreach ($games as $game) {
            \App\Models\Product::create([
                'name' => $game['name'],
                'steam_app_id' => $game['steam_app_id'],
                'price' => $game['price'],
                'is_active' => true,
            ]);
        }
    }
}
