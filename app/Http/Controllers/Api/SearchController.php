<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GlobalSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(protected GlobalSearchService $searchService) {}

    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'q'     => 'required|string|min:2|max:100',
            'limit' => 'nullable|integer|min:1|max:20',
        ]);

        $results = $this->searchService->search(
            $request->q,
            (int) $request->input('limit', 5)
        );

        return response()->json($results);
    }
}
