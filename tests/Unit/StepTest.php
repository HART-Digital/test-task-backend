<?php

namespace Tests\Unit;

use App\Enums\Steps\StepTypes;
use App\Services\Steps\Step;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StepTest extends TestCase
{
    private FilesystemAdapter $storage;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->storage = Storage::disk('public');
    }

    public function testGetJsonType()
    {
        $this->assertContentHasType('{"Furniture": {}}', StepTypes::FURNITURE);
        $this->assertContentHasType('{}', StepTypes::NEURAL);
    }

    private function assertContentHasType(string $contentJson, string $type): void
    {
        $filename = 'plan.json';
        $this->storage->put($filename, $contentJson);
        $file = new UploadedFile($this->storage->path($filename), $filename);
        $this->assertEquals($type, Step::getJsonType($file));
    }
}
