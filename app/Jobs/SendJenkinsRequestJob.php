<?php

namespace App\Jobs;

use App\Models\Plan;
use App\Services\Steps\Step;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendJenkinsRequestJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private array $postData;
    private string $configName;
    private Plan $plan;
    private string $key;

    public function __construct(array $postData, string $configName, Plan $plan, string $key)
    {
        $this->postData = $postData;
        $this->configName = $configName;
        $this->plan = $plan;
        $this->key = $key;
    }

    public function handle()
    {
        $response = Step::http()->post(config($this->configName), $this->postData);
        $this->plan->setAdditionalKey($this->key, $response->status());
        $this->plan->save();
    }
}
