<?php


namespace Tests\Feature\Api;


use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThrottleTest extends TestCase
{
    use RefreshDatabase;

    private const METHOD = 'POST';

    public function testContinue()
    {
        $plan = Plan::factory()->create();
        for ($i = 0; $i < 299; $i++) {
            $this->json(self::METHOD, "/api/steps/$plan->id/continue")->assertStatus(204);
        }
    }
}
