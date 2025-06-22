<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Event;
use App\Models\Khabar;
use App\Models\Scope;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class localSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Scope::factory(3)->create();
        Category::factory(30)->create();
        User::factory(30)->create()->each(function ($user) {
            $posts = Article::factory(rand(1, 4))->create(['user_id' => $user->id]);
            $posts->each(function ($post) use ($user) {
                $comments = rand(2, 5);
                $post->comments()->create([
                    "user_id" => $user->id,
                    "text" => fake()->text()
                ]);

                $user->like($post);

                $post->views()->create([
                    "user_id" => $user->id,
                    "ip_address" => "192.168.1.1"
                ]);
            });

            $events = Event::factory(rand(1, 4))->create(['user_id' => $user->id]);
            $events->each(function ($event) use ($user) {
                $comments = rand(2, 5);
                $event->comments()->create([
                    "user_id" => $user->id,
                    "text" => fake()->text()
                ]);

                $user->like($event);


                $event->views()->create([
                    "user_id" => $user->id,
                    "ip_address" => "192.168.1.1"
                ]);
            });

            $khabars = Khabar::factory(rand(1, 4))->create(['user_id' => $user->id , "scope_id" => rand(1,3)]);
            $khabars->each(function ($khabar) use ($user) {
                $khabar->comments()->create([
                    "user_id" => $user->id,
                    "text" => fake()->text()
                ]);
                $user->like($khabar);

                $khabar->views()->create([
                    "user_id" => $user->id,
                    "ip_address" => "192.168.1.1"
                ]);
            });
        });
    }
}
