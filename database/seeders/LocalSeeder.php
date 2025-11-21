<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Event;
use App\Models\Khabar;
use App\Models\Magazine;
use App\Models\Scope;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocalSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            Scope::factory()->count(3)->create();
            Category::factory()->count(30)->create();
            $scopeIds = Scope::pluck('id')->toArray();

            User::factory()->count(30)->create()->each(function ($user) use ($scopeIds) {
                $user->assignRole('user');
                $magazines = Magazine::factory(rand(1, 4))->create(['user_id' => $user->id]);
                $events = Event::factory(rand(1, 4))->create(['user_id' => $user->id]);
                $khabars = Khabar::factory(rand(1, 4))->create([
                    'user_id' => $user->id,
                    'scope_id' => fake()->randomElement($scopeIds),
                ]);

                $magazines->each(fn ($post) => $this->addRelations($post, $user));
                $magazines->each(function ($magazine) use ($user) {
                    $articles = Article::factory(rand(1, 4))->create(['magazine_id' => $magazine->id]);
                    $articles->each(fn ($article) => $this->addRelations($article, $user));
                });
                $events->each(fn ($event) => $this->addRelations($event, $user));
                $khabars->each(fn ($khabar) => $this->addRelations($khabar, $user));
            });
        });
    }

    private function addRelations($model, $user): void
    {
        $model->comments()->create([
            'user_id' => $user->id,
            'body' => fake()->text(),
        ]);

        $user->like($model);

        $model->views()->create([
            'user_id' => $user->id,
            'ip_address' => '192.168.1.1',
        ]);
    }
}
