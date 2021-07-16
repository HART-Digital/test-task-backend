<?php


namespace Tests\Feature;


use App\DTO\PlanCreateDTO;
use App\VO\PlanOptions\MiddleCutHeight;
use App\VO\PlanOptions\Resolution;
use App\VO\PlanOptions\Styles;
use App\VO\PlanOptions\TopDownViewCount;
use Illuminate\Http\UploadedFile;

class DTOStubs
{
    public static function getPlanCreateDTOStub(): PlanCreateDTO
    {
        $planImage = UploadedFile::fake()->image('some_name.jpg', 512, 512);
        $maskImage = null;

        $log = true;

        $disablePanoramas = false;
        $disableTopViews = false;
        $disableFurniture = false;
        $disableUnreal = false;
        $disableMiddleCut = false;

        $topDownViewCount = TopDownViewCount::create(30);
        $styles = Styles::create([1]);
        $resolution = Resolution::create(4096);
        $middleCutHeight = MiddleCutHeight::create(150);

        return new PlanCreateDTO(
            $planImage,
            $maskImage,
            $log,
            $disablePanoramas,
            $disableTopViews,
            $disableFurniture,
            $disableUnreal,
            $disableMiddleCut,
            $topDownViewCount,
            $styles,
            $resolution,
            $middleCutHeight,
        );
    }
}
