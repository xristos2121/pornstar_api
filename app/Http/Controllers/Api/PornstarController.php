<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PornstarResource;
use App\Http\Resources\PornstarCollection;
use App\Models\Pornstar;
use App\Services\PornstarSearchService;
use App\Http\Requests\Api\PornstarIndexRequest;
use App\Http\Requests\Api\PornstarShowRequest;
use App\Http\Requests\Api\PornstarSearchRequest;
use Illuminate\Support\Facades\Cache;

class PornstarController extends Controller
{
    protected $searchService;

    public function __construct(PornstarSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index(PornstarIndexRequest $request)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 20);
        $sort = $request->input('sort', 'name');
        $filter = $request->input('filter', []);

        $relations = [
            'thumbnails.urls',
            'attributes',
            'stats',
            'hairColors',
            'ethnicities'
        ];

        $cacheKey = "pornstars_" . md5(json_encode($request->all()));

        $pornstars = Cache::remember($cacheKey, 3600, function () use ($page, $perPage, $sort, $filter, $relations) {
            $result = $this->searchService->search(null, $filter, $sort, $perPage, $relations, false);

            if ($result['pornstars']->count() > 0) {
                $result['pornstars']->load('thumbnails.urls');
            }

            return $result['pornstars'];
        });

        return response()->json([
            'data' => new PornstarCollection($pornstars),
            'meta' => [
                'pagination' => [
                    'total' => $pornstars->total(),
                    'count' => $pornstars->count(),
                    'per_page' => $pornstars->perPage(),
                    'current_page' => $pornstars->currentPage(),
                    'total_pages' => $pornstars->lastPage()
                ]
            ],
            'links' => [
                'self' => url()->current(),
                'first' => $pornstars->url(1),
                'last' => $pornstars->url($pornstars->lastPage()),
                'prev' => $pornstars->previousPageUrl(),
                'next' => $pornstars->nextPageUrl()
            ]
        ]);
    }

    public function show(PornstarShowRequest $request, $id)
    {
        $request->validate([
            'fields' => 'nullable|string',
            'include' => 'nullable|string'
        ]);

        $relations = ['thumbnails.urls', 'attributes', 'stats', 'aliases', 'hairColors', 'ethnicities'];

        $cacheKey = "pornstar_{$id}_" . md5(json_encode($request->all()));

        $pornstar = Cache::remember($cacheKey, 3600, function () use ($id, $relations) {
            return Pornstar::with($relations)->findOrFail($id);
        });

        return response()->json([
            'data' => new PornstarResource($pornstar),
            'links' => [
                'self' => url()->current(),
                'collection' => url('/api/v1/pornstars')
            ]
        ]);
    }

    public function search(PornstarSearchRequest $request)
    {
        $searchTerm = $request->input('q');
        $perPage = $request->input('per_page', 20);
        $sort = $request->input('sort', 'name');
        $filter = $request->input('filter', []);

        $relations = [
            'thumbnails.urls',
            'attributes',
            'stats',
            'hairColors',
            'ethnicities'
        ];

        $cacheKey = "pornstar_search_" . md5(json_encode($request->all()));

        $result = Cache::remember($cacheKey, 900, function () use (
            $searchTerm, $filter, $sort, $perPage, $relations
        ) {
            $result = $this->searchService->search(
                $searchTerm,
                $filter,
                $sort,
                $perPage,
                $relations,
                false
            );

            if ($result['pornstars']->count() > 0) {
                $result['pornstars']->load('thumbnails.urls');
            }

            return $result;
        });

        $pornstars = $result['pornstars'];

        $response = [
            'data' => new PornstarCollection($pornstars),
            'meta' => [
                'pagination' => [
                    'total' => $pornstars->total(),
                    'count' => $pornstars->count(),
                    'per_page' => $pornstars->perPage(),
                    'current_page' => $pornstars->currentPage(),
                    'total_pages' => $pornstars->lastPage()
                ]
            ],
            'links' => [
                'self' => url()->current(),
                'first' => $pornstars->url(1),
                'last' => $pornstars->url($pornstars->lastPage()),
                'prev' => $pornstars->previousPageUrl(),
                'next' => $pornstars->nextPageUrl()
            ]
        ];

        return response()->json($response);
    }
}
