<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@cdcms.com',
            'password' => Hash::make('Admin@123'),
            'role'     => 'admin',
            'status'   => 'active',
        ]);

        // Draft Categories in order
        $categories = [
            ['name' => 'Iconic Players',   'slug' => 'iconic-players',   'description' => 'Top-tier legendary players',               'max_players' => 5,  'draft_order' => 1],
            ['name' => 'Platinum Players', 'slug' => 'platinum-players', 'description' => 'Elite players of the tournament',           'max_players' => 8,  'draft_order' => 2],
            ['name' => 'Gold Batsmen',     'slug' => 'gold-batsmen',     'description' => 'Skilled specialist batsmen',                'max_players' => 10, 'draft_order' => 3],
            ['name' => 'Gold Bowlers',     'slug' => 'gold-bowlers',     'description' => 'Skilled specialist bowlers',                'max_players' => 10, 'draft_order' => 4],
            ['name' => 'All Rounders',     'slug' => 'all-rounders',     'description' => 'Versatile players excelling in both',       'max_players' => 8,  'draft_order' => 5],
            ['name' => 'Emerging Players', 'slug' => 'emerging-players', 'description' => 'Promising young talent for the future',     'max_players' => 12, 'draft_order' => 6],
        ];

        foreach ($categories as $cat) {
            Category::create(array_merge($cat, ['is_active' => true]));
        }
    }
}
