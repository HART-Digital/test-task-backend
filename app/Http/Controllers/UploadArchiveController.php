<?php

namespace App\Http\Controllers;

use App\Actions\CreatePlanAction;
use App\DTO\StepsDTO;
use App\Enums\Steps\StepTypes;
use App\Http\Controllers\Api\PlansAPIController;
use App\Services\PlansService\PlansService;
use App\Services\ReloadService;
use App\Utils\PlanUtils;
use Database\Factories\PlanFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\Request;
use App\Models\Plan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Tests\CreatesApplication;
use Tests\Feature\Services\StepsService\StepsServiceTest;
use function React\Promise\all;
use App\Http\Controllers\Api\PlanStepsAPIController;


use App\Enums\Steps\StepStatus;
use App\Events\Plan\PlanCreatedEvent;
use Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Storage;
use Tests\TestCase;

use \Illuminate\Contracts\Foundation\Application;

class UploadArchiveController extends Controller
{
    use CreatesApplication;
    use RefreshDatabase;

    /**
     * The Illuminate application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    private StepsDTO $dto;
    private CreatePlanAction $action;

    public function __construct()
    {
        $this->dto = new StepsDTO();
        $this->app = $this->createApplication();
//        dd($this->app);

        $this->action = $this->app->make(CreatePlanAction::class);
//        $this->action = new CreatePlanAction();
    }

    public function uploadForm()
    {
//        $ch = curl_init();
//
//        curl_setopt($ch, CURLOPT_URL,"http://localhost/");
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        $server_output = curl_exec($ch);
//
//        dd($server_output);

        return view('upload');
    }

    public function uploadFile(Request $request)
    {
        //переделать валидацию
        $request->validate([
            'file' => 'required|mimes:zip|max:4096'
        ]);

        $fileModel = new Plan();
        if ($request->file()) {
            $testPlanFactory = (new PlanFactory())->definition();
            $filePath = $request->file->store('public');
            $fileModel->paths = '/storage/' . $filePath;
            $fileModel->id = Str::orderedUuid()->toString();
            $fileModel->additional = $testPlanFactory['additional'];
            $fileModel->options = $testPlanFactory['options'];
            $fileModel->status = $testPlanFactory['status'];

            $fileModel->save();

            return "Zip file has been upload successfully :)";
        }
        //throw new exeption
//        return "Zip file has been upload successfully :)";
    }

    public function uploadFile2(Request $request) {

        dd(1);

        $this->dto->setSteps([StepTypes::UNREAL]);
//        $plan = UploadedFile::fake()->createWithContent('plan.json', '{"Furniture": []}');
        $plan = $request->file;// UploadedFile::fake()->createWithContent('plan.json', '{"Furniture": []}');

        $this->dto->setJson($plan);
        dd($plan);
        $plan = $this->action->execute($this->dto); //тут краш
        $id = $plan->id;

        dd($id);
        $this->assert($id, StepTypes::UNREAL);

        $type = StepTypes::FURNITURE;
        $dir = PlanUtils::getTypeDir($plan, $type);
        Storage::disk('public')->assertExists("{$dir}/{$type}.json");

    }

    public function uploadFile3(Request $request)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"http://127.0.0.1:8000/api/plans");
//        curl_setopt($ch, CURLOPT_POST, 0);

        $server_output = curl_exec($ch);

        dd($server_output);
    }
}
