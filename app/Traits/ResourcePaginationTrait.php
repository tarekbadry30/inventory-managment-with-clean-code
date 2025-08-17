<?php

namespace App\Traits;

trait ResourcePaginationTrait
{
    /**
     * Paginate a resource collection.
     */
    public static function withPagination($collection)
    {
        //check if collection is pagination instance
        if ($collection instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return [
                'items' => self::collection($collection),
                'pagination' => [
                    'total' => $collection->total(),
                    'count' => $collection->count(),
                    'per_page' => $collection->perPage(),
                    'current_page' => $collection->currentPage(),
                    'total_pages' => $collection->lastPage(),
                    'next_page_url' => $collection->nextPageUrl(),
                    'prev_page_url' => $collection->previousPageUrl(),
                ],
            ];
        }
        return $collection;
    }
}
