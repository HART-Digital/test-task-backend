<?php

namespace App\Providers\Macros;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class NormalizedPaginateMacroProvider extends ServiceProvider
{
    private const METHOD_NAME = 'normalizedPaginate';

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        EloquentBuilder::macro(
            self::METHOD_NAME,
            $this->normalizedPaginate()
        );

        QueryBuilder::macro(
            self::METHOD_NAME,
            $this->normalizedPaginate()
        );
    }

    private function normalizedPaginate(): callable
    {
        return function (
            callable $modifyItems = null,
            int $perPage = 15,
            array $columns = ['*'],
            string $pageName = 'page',
            int $page = null
        ) {
            $paginatedCollection = $this->paginate($perPage, $columns, $pageName, $page);

            $items = $modifyItems
                ? array_map($modifyItems, $paginatedCollection->items())
                : $paginatedCollection->items();

            return [
                'items' => $items,
                'currentPage' => $paginatedCollection->currentPage(),
                'lastPage' => $paginatedCollection->lastPage(),
                'perPage' => $paginatedCollection->perPage(),
                'total' => $paginatedCollection->total(),
            ];
        };
    }
}
