<?php

namespace Tests\Feature\Actions;

use App\Actions\CreatePlanAction;
use App\DTO\StepsDTO;
use App\Enums\Steps\StepStatus;
use App\Events\Plan\PlanCreatedEvent;
use App\Enums\Steps\StepTypes;
use App\Utils\PlanUtils;
use Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;

class CreatePlanActionTest extends TestCase
{
    use RefreshDatabase;

    private StepsDTO $dto;
    private CreatePlanAction $action;


    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Event::fake();
        $this->dto = new StepsDTO();
        $this->action = $this->app->make(CreatePlanAction::class);
    }

    protected function tearDown(): void
    {
        Event::assertDispatched(PlanCreatedEvent::class);
        parent::tearDown();
    }

    public function testCreateWithNeural()
    {
        $this->dto->setSteps([StepTypes::NEURAL]);
        $plan = UploadedFile::fake()->image('plan.jpg', 128);
        $this->dto->setPlan($plan);

        $plan = $this->action->execute($this->dto);
        $id = $plan->id;

        $this->assert($id, StepTypes::NEURAL);

        $type = StepTypes::PLAN;
        $dir = PlanUtils::getTypeDir($plan, $type);
        Storage::disk('public')->assertExists("{$dir}/{$type}.jpg");
    }

    private function assert(string $id, string $type): void
    {
        $this->assertDatabaseHas(
            'plans',
            [
                'id' => $id,
                'status' => json_encode(
                    [
                        $type => StepStatus::WAIT,
                    ]
                ),
                'additional' => json_encode(
                    [
                        'currentStep' => $type,
                    ]
                ),
            ]
        );
    }

    public function testCreateWithFurniture()
    {
        $this->dto->setSteps([StepTypes::FURNITURE]);
        $plan = UploadedFile::fake()->create('plan.json', 10);
        $this->dto->setJson($plan);

        $plan = $this->action->execute($this->dto);
        $id = $plan->id;

        $this->assert($id, StepTypes::FURNITURE);

        $type = StepTypes::NEURAL;
        $dir = PlanUtils::getTypeDir($plan, $type);
        Storage::disk('public')->assertExists("{$dir}/{$type}.json");
    }

    public function testCreateWithUnreal()
    {
        $this->dto->setSteps([StepTypes::UNREAL]);
        $plan = UploadedFile::fake()->createWithContent('plan.json', '{"Furniture": []}');
        $this->dto->setJson($plan);

        $plan = $this->action->execute($this->dto);
        $id = $plan->id;

        $this->assert($id, StepTypes::UNREAL);

        $type = StepTypes::FURNITURE;
        $dir = PlanUtils::getTypeDir($plan, $type);
        Storage::disk('public')->assertExists("{$dir}/{$type}.json");
    }
}
