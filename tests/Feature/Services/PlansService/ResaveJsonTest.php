<?php


namespace Tests\Feature\Services\PlansService;


use App\Models\Plan;
use App\Enums\Steps\StepTypes;
use App\Services\PlansService\PlansService;
use Database\Factories\PlanFactory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ResaveJsonTest extends TestCase
{
    use RefreshDatabase;

    private PlanFactory $planFactory;
    private PlansService $plansService;
    private Filesystem $storage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = \Storage::fake('public');

        $this->planFactory = new PlanFactory;
        $this->plansService = new PlansService;
    }

    public function testResave(): void
    {
        $neuralJson = UploadedFile::fake()->createWithContent('neural.json', '{"oldJson": true}');
        $neuralJsonName = StepTypes::NEURAL . '.json';
        $folder = 'path/to';
        $neuralPath = $this->storage->putFileAs($folder, $neuralJson, $neuralJsonName);

        $plan = Plan::factory()->create(
            [
                'paths' => [
                    'neural' => $neuralPath,
                ],
            ]
        );

        $json = '{"newJson": true}';

        $this->plansService->resaveJson($json, $plan->id);

        $currentJsonContent = $this->storage->get($neuralPath);

        $this->assertTrue($this->storage->exists($neuralPath));
        $this->assertEquals($json, $currentJsonContent);
        $this->assertCount(2, $this->storage->files($folder));
    }
}
