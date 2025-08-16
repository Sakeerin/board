<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\BoardList;
use App\Models\Card;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create(['email' => 'demo@example.com']);
        $board = Board::create(['name' => 'Demo Board', 'owner_id' => $user->id]);
        $board->members()->attach($user->id, ['role' => 'owner']);

        $todo = BoardList::create(['board_id' => $board->id, 'name' => 'To do', 'position' => 1000]);
        $doing = BoardList::create(['board_id' => $board->id, 'name' => 'Doing', 'position' => 2000]);
        $done = BoardList::create(['board_id' => $board->id, 'name' => 'Done', 'position' => 3000]);

        foreach (['Plan', 'Design', 'Build', 'Test'] as $i => $title) {
            Card::create(['board_list_id' => $todo->id, 'title' => $title, 'position' => ($i+1)*1000, 'owner_id' => $user->id]);
        }
    }
}
