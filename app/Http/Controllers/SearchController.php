<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
        public function __invoke(SearchRequest $request)
    {
        $data = $request->validated([
        ]);

        $search = $data["search"] ?? null;
        $type = $data["type"] ?? 'all';
        $page = $request->input('page', 1);
        $perPage = 10;

        try {
            if ($type === 'all') {
                $models = [
                    Magazine::query(),
                    Event::query(),
                    Khabar::query(),
                    Article::query()
                ];

                if ($search) {
                    foreach ($models as $model) {
                        $model->where('title', 'like', "%{$search}%");
                    }
                }

                $results = collect();

                foreach ($models as $model) {
                    $results = $results->merge($model->get());
                }

                $paginated = new LengthAwarePaginator(
                    $results->forPage($page, $perPage),
                    $results->count(),
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );

            } else {
                $model = match ($type) {
                    'Magazine' => Magazine::query(),
                    'Event' => Event::query(),
                    'New' => Khabar::query(),
                    'Article' => Article::query(),
                    default => Magazine::query(),
                };

                if ($search) {
                    $model->where('title', 'like', "%{$search}%");
                }

                $paginated = $model->paginate($perPage);
            }

        } catch (\Exception $e) {
            Log::error("Search error: " . $e->getMessage());
            $paginated = Magazine::paginate($perPage);
        }

        return view("main.search", ['results' => $paginated]);
    }
}
