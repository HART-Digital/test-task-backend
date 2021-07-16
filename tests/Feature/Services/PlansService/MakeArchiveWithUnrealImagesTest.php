<?php

namespace Tests\Feature\Services\PlansService;

use App\Models\Plan;
use App\Enums\Steps\StepTypes;
use App\Services\PlansService\PlansService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Storage;
use ZipArchive;

class MakeArchiveWithUnrealImagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var PlansService
     */
    private $ps;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ps = new PlansService;
    }

    /**
     * @test
     */
    public function makeArchiveWithUnrealImages()
    {
        Storage::fake('public');

        $plan = $this->preparePlan();

        $this->ps->makeArchiveWithUnrealImages($plan->id);

        $archiveName = "archives/projects/{$plan->id}.zip";

        Storage::disk('public')->assertExists($archiveName);
    }

    private function preparePlan(): Plan
    {
        $plan = new Plan();

        $plan->save();

        $this->addFakeUnrealImageToPlan($plan);

        return $plan;
    }

    private function addFakeUnrealImageToPlan(Plan $plan)
    {
        $panoramaName = 'Panorama_0_0_1.jpg';
        $panoramaImage = UploadedFile::fake()->image($panoramaName, 512, 512);

        $storage = Storage::disk('public');

        $unrealDir = "projects/{$plan->id}/unreal/Style_01";

        $path = $storage->putFileAs($unrealDir, $panoramaImage, $panoramaName);

        $plan->setPathsKey(StepTypes::UNREAL, [$path]);

        $plan->save();
    }
}
