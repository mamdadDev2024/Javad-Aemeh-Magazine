<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\Magazine;
use App\Models\Event;
use App\Models\Khabar;
use App\Models\Article;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{

    public function __invoke(SearchRequest $request)
    {
        $data = $request->validated();
        $search = $data['search'] ?? null;
        $type = $data['type'] ?? 'all';
        $page = $request->input('page', 1);
        $perPage = 10;

        try {
            if ($type === 'all') {
                $results = collect([
                    Magazine::query(),
                    Event::query(),
                    Khabar::query(),
                    Article::query()
                ])->flatMap(function ($query) use ($search) {
                    return $query->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
                                ->get();
                });

                $results = $results->sortByDesc('created_at')->values();

                $paginated = new LengthAwarePaginator(
                    $results->forPage($page, $perPage),
                    $results->count(),
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            } else {
                $modelMap = [
                    'Magazine' => Magazine::class,
                    'Event' => Event::class,
                    'Khabar' => Khabar::class, // اصلاح کلید
                    'Article' => Article::class,
                ];

                $query = ($modelMap[$type] ?? Magazine::class)::query();
                $query->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"));
                $paginated = $query->latest()->paginate($perPage);
            }

        } catch (\Exception $e) {
            Log::error('Search error', [
                'message' => $e->getMessage(),
                'type' => $type,
                'search' => $search
            ]);
            $paginated = Magazine::latest()->paginate($perPage);
        }

        return view('main.search', [
            'results' => $paginated,
            'search' => $search,
            'type' => $type
        ]);
    }
}
