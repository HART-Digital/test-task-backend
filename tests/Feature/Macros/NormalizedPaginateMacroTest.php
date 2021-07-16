<?php

namespace Tests\Feature\Macros;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DB;
use Tests\TestCase;

class NormalizedPaginateMacroTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->count(30)->create();
    }

    public function testNormalizedPaginateWithQueryBuilder(): void
    {
        $paginated = DB::table('users')->normalizedPaginate();
        $this->assertNormalizedPaginateKeys($paginated);
    }

    private function assertNormalizedPaginateKeys(array $paginated): void
    {
        $keys = collect(
            [
                'items',
                'currentPage',
                'lastPage',
                'perPage',
                'total',
            ]
        );

        $keys->each(fn(string $key) => $this->assertArrayHasKey($key, $paginated));
        $this->assertCount($keys->count(), $paginated);
    }

    public function testNormalizedPaginateWithEloquentBuilder(): void
    {
        $paginated = User::normalizedPaginate();
        $this->assertNormalizedPaginateKeys($paginated);
    }
}
