<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\Article;
use App\Models\Event;
use App\Models\Khabar;
use App\Models\Magazine;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    protected array $modelMap = [
        'Magazine' => Magazine::class,
        'Event' => Event::class,
        'Khabar' => Khabar::class,
        'Article' => Article::class,
    ];

    public function __invoke(SearchRequest $request)
    {
        $data = $request->validated();
        $search = $data['search'] ?? null;
        $type = $data['type'] ?? 'all';
        $page = $request->input('page', 1);
        $perPage = 10;

        try {
            if ($type === 'all') {
                $results = collect($this->modelMap)
                    ->flatMap(function ($model) use ($search) {
                        return $model::query()
                            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
                            ->get();
                    })
                    ->sortByDesc('created_at')
                    ->values();

                $paginated = new LengthAwarePaginator(
                    $results->forPage($page, $perPage),
                    $results->count(),
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            } elseif (array_key_exists($type, $this->modelMap)) {
                $model = $this->modelMap[$type];
                $paginated = $model::query()
                    ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
                    ->latest()
                    ->paginate($perPage);
            } else {
                $paginated = collect()->paginate($perPage);
            }

        } catch (\Exception $e) {
            Log::error('Search error', [
                'message' => $e->getMessage(),
                'type' => $type,
                'search' => $search,
            ]);

            $paginated = Magazine::latest()->paginate($perPage);
        }

        return view('main.search', [
            'results' => $paginated,
            'search' => $search,
            'type' => $type,
        ]);
    }
}
