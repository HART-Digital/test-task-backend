<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanRequest;
use App\Models\PlanTest;
use App\Services\StorePlanService;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index(PlanTest $plans)
    {
        return view('test.test', compact('plans'));
    }

    public function upload(StorePlanRequest $request, StorePlanService $service)
    {
        $service->validation($request);

        return redirect('/test');
    }
}
