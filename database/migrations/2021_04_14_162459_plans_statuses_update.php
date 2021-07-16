<?php

use App\Enums\Steps\StepStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PlansStatusesUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $plans = DB::table('plans')->get();

        try {
            DB::beginTransaction();
            foreach ($plans as $plan) {
                $status = json_decode($plan->status, true);
                $steps = [];
                $newStatus = [];
                if (isset($status['neural'])) {
                    $steps[] = 'neural';
                    $newStatus['neural'] = $status['neural'] === 1
                        ? StepStatus::FINISH
                        : StepStatus::ERROR;
                }
                if (isset($status['furniture'])) {
                    $steps[] = 'furniture';
                    $newStatus['furniture'] = $status['furniture'] === 1
                        ? StepStatus::FINISH
                        : StepStatus::ERROR;
                }
                if (isset($status['unreal'])) {
                    $steps[] = 'unreal';
                    $newStatus['unreal'] = $status['unreal'] === 1
                        ? StepStatus::FINISH
                        : StepStatus::ERROR;
                }

                $options = json_decode($plan->options, true);
                $options['steps'] = $steps;

                DB::table('plans')->where('id', $plan->id)->update(
                    [
                        'status' => $newStatus,
                        'options' => json_encode($options),
                    ]
                );
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
