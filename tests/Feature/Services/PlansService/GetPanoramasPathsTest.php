<?php


namespace Tests\Feature\Services\PlansService;

use App\Models\Plan;
use App\Services\PlansService\PlansService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetPanoramasPathsTest extends TestCase
{
    use RefreshDatabase;

    private PlansService $plansService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plansService = new PlansService();
    }

    /**
     * @test
     * @throws \Exception
     */
    public function testGetPanoramas()
    {
        $plan = Plan::factory()->create(
            [
                'paths' => [
                    'plan' => '/plan/img.json',
                    'neural' => '/plan/link.json',
                    'unreal' => [
                        'one/two',
                        'panorama.png',
                        'Panorama_0.png',
                    ],
                ],
            ]
        );
        $result = $this->plansService->getPanoramasPaths($plan);

        $this->assertArrayHasKey('panoramas', $result);
        $this->assertArrayHasKey('plan', $result);
        $this->assertArrayHasKey('json', $result);

        foreach ($result['panoramas'] as $panorama) {
            $this->assertStringContainsStringIgnoringCase('panorama', $panorama);
        }
    }
}
