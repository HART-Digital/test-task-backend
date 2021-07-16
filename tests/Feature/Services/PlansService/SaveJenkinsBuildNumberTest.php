<?php


namespace Tests\Feature\Services\PlansService;


use App\Models\Plan;
use App\Services\PlansService\PlansService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaveJenkinsBuildNumberTest extends TestCase
{
    use RefreshDatabase;

    private PlansService $plansService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->plansService = $this->app->make(PlansService::class);
    }

    public function testSaveJenkinsLog(): void
    {
        $plan = Plan::factory()->create();
        $ad = $plan->additional;
        $ad['jobName_build_number'] = 123;
        $plan = $this->plansService->saveJenkinsBuildNumber($plan->id, 123, 'jobName');

        $this->assertDatabaseHas(
            'plans',
            [
                'id' => $plan->id,
                'additional' => json_encode($ad)
            ]
        );
    }
}
