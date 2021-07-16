<?php

namespace Tests\Feature\Services\StepsService;

use App\Actions\CreatePlanAction;
use App\DTO\StepsDTO;
use App\Enums\Steps\StepStatus;
use App\Events\PlanEvent;
use App\Jobs\SendJenkinsRequestJob;
use App\Models\Plan;
use App\Enums\Steps\StepTypes;
use App\Services\StepsNotificationsService;
use App\Services\StepsService;
use App\Utils\PlanUtils;
use Event;
use Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Queue;
use Storage;
use Tests\TestCase;

class StepsServiceTest extends TestCase
{
    use RefreshDatabase;

    private StepsService $stepsService;
    private CreatePlanAction $createPlanAction;
    private Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Http::fake();
        Bus::fake();
        Event::fake();
        Queue::fake();

        $this->stepsService = $this->app->make(StepsService::class);
        $this->createPlanAction = $this->app->make(CreatePlanAction::class);
    }

    private function reset(): void
    {
        $stepsNotificationsService = new StepsNotificationsService();
        $this->stepsService = new StepsService($stepsNotificationsService);
    }

    public function testNeural()
    {
        $dto = new StepsDTO();

        $dto
            ->setSteps([StepTypes::NEURAL])
            ->setPlan(UploadedFile::fake()->image('plan.jpg', 512));

        $plan = $this->createPlanAction->execute($dto);

        $this->plan = $this->stepsService->start($plan->id);

        Bus::assertDispatched(SendJenkinsRequestJob::class);

        $this->assertDatabaseHas(
            'plans',
            [
                'id' => $this->plan->id,
                'status->neural' => StepStatus::PROCESS,
            ]
        );
    }

    public function testFurniture()
    {
        $dto = new StepsDTO();

        $dto
            ->setSteps([StepTypes::FURNITURE])
            ->setJson(UploadedFile::fake()->create('plan.json', 5));

        $plan = $this->createPlanAction->execute($dto);

        $this->plan = $this->stepsService->start($plan->id);

        Bus::assertDispatched(SendJenkinsRequestJob::class);

        $this->assertDatabaseHas(
            'plans',
            [
                'id' => $this->plan->id,
                'status->furniture' => StepStatus::PROCESS,
            ]
        );
    }

    public function testStartUnreal()
    {
        $dto = new StepsDTO();

        $dto
            ->setSteps(['unreal'])
            ->setJson(UploadedFile::fake()->create('plan.json', 5));

        $plan = $this->createPlanAction->execute($dto);

        $this->plan = $this->stepsService->start($plan->id);

        Bus::assertDispatched(SendJenkinsRequestJob::class);

        $this->assertDatabaseHas(
            'plans',
            [
                'id' => $this->plan->id,
                'status->unreal' => StepStatus::PROCESS,
            ]
        );
    }

    public function testFinishUnreal()
    {
        $dto = new StepsDTO();

        $dto
            ->setSteps([StepTypes::UNREAL])
            ->setIsUnrealPanoramasCaptureEnabled(true)
            ->setIsUnrealTopViewsCaptureEnabled(true)
            ->setJson(UploadedFile::fake()->create('plan.json', 5));

        $plan = $this->createPlanAction->execute($dto);

        $this->plan = $this->stepsService->start($plan->id);

        $path = PlanUtils::getTypeDir($this->plan, StepTypes::UNREAL) . "/Style_0/";

        $panorama1 = UploadedFile::fake()->image('Panorama_0_0_0.jpg', 512);
        $panorama2 = UploadedFile::fake()->image('Panorama_0_0_1.jpg', 512);

        $topView = UploadedFile::fake()->image('TopDownView_0_0_0.jpg', 512);

        Storage::disk('public')->putFileAs($path, $panorama1, 'Panorama_0_0_0.jpg');
        Storage::disk('public')->putFileAs($path, $panorama2, 'Panorama_0_0_1.jpg');

        Storage::disk('public')->putFileAs($path, $topView, 'TopDownView_0_0_0.jpg');

        $this->plan = $this->stepsService->continue($this->plan->id);
        $this->assertDatabaseHas(
            'plans',
            [
                'id' => $this->plan->id,
                'status->unreal' => StepStatus::FINISH,
                'hidden_paths' => json_encode($this->plan->hidden_paths),
            ]
        );
    }


    public function testNeuralFurniture()
    {
        $dto = new StepsDTO();

        $dto
            ->setSteps(['neural', 'furniture'])
            ->setPlan(UploadedFile::fake()->image('plan.jpg', 512));

        $plan = $this->createPlanAction->execute($dto);

        $this->plan = $this->stepsService->start($plan->id);

        $this->reset();

        $this->saveNeuralFiles();

        $this->stepsService->continue($this->plan->id);

        Event::assertDispatched(PlanEvent::class);

        $this->assertDatabaseHas(
            'plans',
            [
                'id' => $this->plan->id,
                'status' => json_encode(
                    [
                        StepTypes::NEURAL => StepStatus::FINISH,
                        StepTypes::FURNITURE => StepStatus::PROCESS,
                    ]
                ),
            ]
        );
    }

    private function saveNeuralFiles(): string
    {
        $path = PlanUtils::getTypeDir($this->plan, StepTypes::NEURAL);

        $file = UploadedFile::fake()->create('something.json', 5);
        return Storage::disk('public')->putFileAs($path, $file, 'neural.json');
    }

    public function testNeuralFurnitureUnreal()
    {
        $dto = new StepsDTO();

        $dto
            ->setSteps(['neural', 'furniture', 'unreal'])
            ->setPlan(UploadedFile::fake()->image('plan.jpg', 512));

        $plan = $this->createPlanAction->execute($dto);

        $this->plan = $this->stepsService->start($plan->id);

        $this->saveNeuralFiles();
        $this->reset();

        $this->stepsService->continue($this->plan->id);
        $this->saveFurnitureFiles();
        $this->reset();

        Event::assertDispatched(PlanEvent::class);

        $this->stepsService->continue($this->plan->id);

        Event::assertDispatched(PlanEvent::class);

        $this->assertDatabaseHas(
            'plans',
            [
                'id' => $this->plan->id,
                'status->neural' => StepStatus::FINISH,
                'status->furniture' => StepStatus::FINISH,
                'status->unreal' => StepStatus::PROCESS,
            ]
        );
    }

    private function saveFurnitureFiles(): string
    {
        $path = PlanUtils::getTypeDir($this->plan, StepTypes::FURNITURE);

        $file = UploadedFile::fake()->create('something.json', 5);
        return Storage::disk('public')->putFileAs($path, $file, 'furniture.json');
    }

    public function testStartUnrealAfterFurniture(): void
    {
        $this->preparePlanForStartUnrealAfterFurniture();

        $this->saveFurnitureFiles();

        $this->stepsService->continue($this->plan->id);

        $this->assertDatabaseHas(
            'plans',
            [
                'id' => $this->plan->id,
                'status->furniture' => StepStatus::FINISH,
                'status->unreal' => StepStatus::PROCESS,
            ]
        );

        Bus::assertDispatched(SendJenkinsRequestJob::class);
    }

    private function preparePlanForStartUnrealAfterFurniture()
    {
        $this->plan = Plan::factory()->make(
            [
                'hidden_paths' => [],
                'status' => [
                    StepTypes::NEURAL => StepStatus::FINISH,
                    StepTypes::FURNITURE => StepStatus::PROCESS,
                    StepTypes::UNREAL => StepStatus::WAIT,
                ],
            ]
        );

        $this->plan->setPathsKey(StepTypes::PLAN, 'path/to/plan');
        $this->plan->setPathsKey(StepTypes::NEURAL, $this->saveNeuralFiles());
        $this->plan->setCurrentStep(StepTypes::FURNITURE);
        $this->plan->setOptionsKey(
            'steps',
            [
                StepTypes::NEURAL,
                StepTypes::FURNITURE,
                StepTypes::UNREAL,
            ]
        );

        $this->plan->save();
    }
}
