<?php


namespace Tests\Feature\Services\ReloadService;

use App\Events\Plan\PlanCreatedEvent;
use App\Models\Plan;
use App\Enums\Steps\StepTypes;
use App\Services\ReloadService;
use Event;
use Http;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Storage;
use Tests\TestCase;

class SetDtoForReloadTest extends TestCase
{
    use RefreshDatabase;

    private ReloadService $reloadService;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Http::fake();
        Bus::fake();
        Event::fake();
        $this->reloadService = $this->app->make(ReloadService::class);
    }

    public function testSet()
    {
        $planImage = UploadedFile::fake()->image('plan.jpeg', 512);
        $maskImage = UploadedFile::fake()->image('mask.jpeg', 512);
        $planImageName = StepTypes::PLAN . '.jpeg';
        $maskImageName = StepTypes::MASK . '.png';
        $storage = Storage::disk('public');
        $planPath = $storage->putFileAs('path/to', $planImage, $planImageName);
        $maskPath = $storage->putFileAs('path/to', $maskImage, $maskImageName);

        $plan = Plan::factory()->create(
            [
                'paths' => [
                    'plan' => $planPath,
                    'mask' => $maskPath,
                ],
            ]
        );

        $reloadedPlan = $this->reloadService->reload($plan->id);

        Event::assertDispatched(PlanCreatedEvent::class);

        $this->assertEquals(
            $plan->options['unrealMiddleCutHeight'],
            $reloadedPlan->options['unrealMiddleCutHeight'],
        );
        $this->assertEquals(
            $plan->options['unrealStyles'],
            $reloadedPlan->options['unrealStyles'],
        );

        $this->assertEquals(
            $plan->options['unrealTopDownViewCount'],
            $reloadedPlan->options['unrealTopDownViewCount'],
        );

        $this->assertEquals(
            $plan->options['isNeuralLogEnabled'],
            $reloadedPlan->options['isNeuralLogEnabled'],
        );
    }
}
