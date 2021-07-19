<?php

namespace App\Services;

use App\Models\PlanTest;

class StorePlanService {

    public $zip;
    public $plan;

    public function __construct(\ZipArchive $zip, PlanTest $plan)
    {
        $this->zip = $zip;
        $this->plan = $plan;
    }

    public function store($request)
    {
        $file = $request->file('file');

        $this->zip->open($file->path());

        $this->zip->extractTo(storage_path() . '/uploads/' . time() . '/');

        $filesInside = [];

        for ($i = 0; $i < $this->zip->count(); $i++) {
            $path = '/uploads/' . time() . '/' . $this->zip->getNameIndex($i);
            if (str_contains($path, '.jpg')) {
                array_push($filesInside, $path);
            }
        }

        $this->save($filesInside, $request);

        return $filesInside;
    }

    protected function save($filesInside, $request)
    {
        $this->plan->plan_id = $request->plan_id;
        $this->plan->paths = json_encode($filesInside, true);

        return $this->plan->save();
    }
}
