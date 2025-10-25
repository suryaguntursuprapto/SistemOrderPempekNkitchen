<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $menus = [
            [
                'name' => 'Pempek Lenjer',
                'description' => 'Pempek berbentuk memanjang dengan tekstur kenyal dan rasa gurih. Disajikan dengan kuah cuko yang asam pedas.',
                'price' => 15000,
                'category' => 'pempek',
                'is_available' => true,
            ],
            [
                'name' => 'Pempek Kapal Selam',
                'description' => 'Pempek berisi telur ayam utuh di dalamnya. Favorit pelanggan dengan rasa yang unik dan mengenyangkan.',
                'price' => 20000,
                'category' => 'pempek',
                'is_available' => true,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}