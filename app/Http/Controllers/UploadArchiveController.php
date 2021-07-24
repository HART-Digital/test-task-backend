<?php

namespace App\Http\Controllers;

use App\Actions\CreatePlanAction;
use Database\Factories\PlanFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\Request;
use App\Models\Plan;
use Illuminate\Support\Str;
use Tests\Feature\Services\StepsService\StepsServiceTest;
use function React\Promise\all;
use App\Http\Controllers\Api\PlanStepsAPIController;


class UploadArchiveController extends Controller
{
    public function uploadForm()
    {
        return view('upload');
    }

    public function uploadFile(Request $request)
    {
        //кривая валидация
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
}
