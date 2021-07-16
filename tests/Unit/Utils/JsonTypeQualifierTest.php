<?php

namespace Tests\Unit\Utils;

use App\Enums\Steps\StepTypes;
use App\Utils\JsonTypeQualifier;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\TestCase;

class JsonTypeQualifierTest extends TestCase
{
    public function testQualifyContent()
    {
        $content = '{}';
        $furnitureContent = '{"Furniture": []}';

        $this->assertEquals(StepTypes::NEURAL, JsonTypeQualifier::qualifyContent($content));
        $this->assertEquals(StepTypes::FURNITURE, JsonTypeQualifier::qualifyContent($furnitureContent));
    }

    public function testQualifyUploadedFile()
    {
        $file = UploadedFile::fake()->createWithContent('name.json', '{}');

        $this->assertEquals(StepTypes::NEURAL, JsonTypeQualifier::qualifyUploadedFile($file));
    }
}
