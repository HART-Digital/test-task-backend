<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PlanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Plan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Str::uuid()->toString(),
            'paths' => [
                'mask' => 'path/to/mask',
                'furniture' => null,
                'logs' => [],
                'plan' => 'path/to/plan',
                'neural' => 'path/to/neural',
                'unreal' => [
                    'path/to/unreal/1',
                    'path/to/unreal/2',
                    'path/to/unreal/3',
                    'path/to/unreal/4',
                    'path/to/unreal/5',
                    'path/to/unreal/6',
                    'path/to/unreal/7',
                    'path/to/unreal/8',
                ],
            ],
            'hidden_paths' => [
                'path/to/unreal/4',
                'path/to/unreal/7',
                'path/to/unreal/8',
            ],
            'options' => [
                'isNeuralLogEnabled' => false,
                'isUnrealPanoramasCaptureEnabled' => false,
                'isUnrealTopViewsCaptureEnabled' => false,
                'unrealTopDownViewCount' => 1,
                'isUnrealMiddleCutCaptureEnabled' => false,
                'unrealMiddleCutHeight' => 150,
                'unrealResolution' => 2048,
                'unrealStyles' => '0',
                'steps' => ['neural'],
            ],
            'status' => [
                'neural' => 2,
            ],
            'additional' => [
                'currentStep' => null,
            ],
            'created_at' => now()->toString(),
            'updated_at' => now()->toString(),
            'user_id' => null,
            'public' => (bool)rand(0, 1)
        ];
    }
}
