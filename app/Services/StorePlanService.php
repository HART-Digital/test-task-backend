<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\PlanTest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class StorePlanService {

    public $zip;
    public $plan;
    public $storage;
    public $approved_plan;

    public function __construct(\ZipArchive $zip, Storage $storage, Plan $plan)
    {
        $this->zip = $zip;
        $this->storage = $storage;
        $this->plan = $plan;
    }

    public function validation($request)
    {
        if(!$this->plan::find($request->plan_id)) {
            return Redirect::back()->with(['msg' => 'Не удалось найти план с таким UUID']);
        }

        $this->approved_plan = $this->plan::find($request->plan_id);
        return $this->store($request);
    }

    protected function store($request)
    {

        $file = $request->file('file');

        $archivesDir = 'archives/projects';

        $storage = $this->storage::disk('public');

        $this->zip->open($file->path());

        $this->zip->extractTo($storage->path($archivesDir) . '/' . $request->plan_id . '/');

        $filesInside = [];

        for ($i = 0; $i < $this->zip->count(); $i++) {
            $path = $storage->path($archivesDir) . '/' . $request->plan_id . '/' . $this->zip->getNameIndex($i);
            if (str_contains($path, '.jpg')) {
                array_push($filesInside, $path);
            }
        }

        $this->save($filesInside, $request);

        return $filesInside;
    }

    protected function save($filesInside, $request)
    {
        $this->approved_plan->id = $request->plan_id;
        $this->approved_plan->paths = $filesInside;

        return $this->plan->save();
    }
}
