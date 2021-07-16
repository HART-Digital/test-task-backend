<?php

namespace Tests\Feature\Services\PlansService;

use App\Models\Plan;
use App\Services\PlansService\PlansService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Storage;
use ZipArchive;

class DeleteTest extends TestCase
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
     * @throws \Exception
     */
    public function deleteTest()
    {
        $plan = Plan::create();
        $id = $plan->id;

        $this->ps->delete($id);

        $this->assertDatabaseMissing('plans', [
            'id' => $id,
        ]);
    }

    /**
     * @test
     */
    public function projectsDirIsDeleted()
    {
        Storage::fake('public');
        $storage = Storage::disk('public');

        $plan = Plan::create();

        $dir = $this->ps->getPlanProjectsDir($plan);
        $storage->createDir($dir);

        $this->ps->delete($plan->id);

        $storage->assertMissing($dir);
    }

    /**
     * @test
     */
    public function logsDirIsDeleted()
    {
        Storage::fake('public');
        $storage = Storage::disk('public');

        $plan = Plan::create();

        $dir = $this->ps->getPlanLogsDir($plan);
        $storage->createDir($dir);

        $this->ps->delete($plan->id);

        $storage->assertMissing($dir);
    }

    /**
     * @test
     */
    public function previewsDirIsDeleted()
    {
        Storage::fake('public');
        $storage = Storage::disk('public');

        $plan = Plan::create();

        $dir = $this->ps->getPlanPreviewsDir($plan);
        $storage->createDir($dir);

        $this->ps->delete($plan->id);

        $storage->assertMissing($dir);
    }

    /**
     * @test
     */
    public function archiveIsDeleted()
    {
        Storage::fake('public');
        $storage = Storage::disk('public');
        $storage->createDir('archives/projects');

        $plan = Plan::create();
        $archiveName = $this->ps->getPlanUnrealImagesArchiveName($plan);
        $path = $storage->path($archiveName);
        $this->createZipArchiveWithPath($path);

        $this->ps->delete($plan->id);

        $storage->assertMissing($archiveName);
    }

    private function createZipArchiveWithPath(string $path)
    {
        $zip = new ZipArchive;
        $zip->open($path, ZipArchive::CREATE);
        $zip->addFromString('Panorama_0_0_1.jpg', 'content');
        $zip->close();
    }
}
