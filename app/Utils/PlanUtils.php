<?php

namespace App\Utils;

use App\Models\Plan;
use App\Enums\Steps\StepTypes;

class PlanUtils
{
    public static function getTypeDir(Plan $plan, string $type): string
    {
        $id = $plan->id;
        $date = $plan->created_at === null ? date('Ym') : $plan->created_at->format('Ym');
        $base = "projects/{$date}/{$id}";

        $dirs = [
            StepTypes::PLAN => $base,
            StepTypes::MASK => $base,
            StepTypes::NEURAL => $base . DIRECTORY_SEPARATOR . StepTypes::NEURAL,
            StepTypes::FURNITURE => $base . DIRECTORY_SEPARATOR . StepTypes::FURNITURE,
            StepTypes::UNREAL => $base . DIRECTORY_SEPARATOR . StepTypes::UNREAL,
            StepTypes::LOGS => StepTypes::LOGS . DIRECTORY_SEPARATOR . $date . DIRECTORY_SEPARATOR . $id,
        ];

        return $dirs[$type];
    }
}
