<?php

namespace App\Http\Requests;

use App\DTO\StepsDTO;
use App\VO\PlanOptions\MiddleCutHeight;
use App\VO\PlanOptions\Resolution;
use App\VO\PlanOptions\Styles;
use App\VO\PlanOptions\TopDownViewCount;
use Illuminate\Foundation\Http\FormRequest;

class StartStepsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'steps' => 'required|array',

            'plan' => 'image|nullable',
            'mask' => 'image|nullable',
            'isNeuralLogEnabled' => 'boolean',

            'json' => 'file|nullable',

            'isUnrealPanoramasCaptureEnabled' => 'boolean',
            'isUnrealTopViewsCaptureEnabled' => 'boolean',
            'unrealTopDownViewCount' => 'integer',
            'isUnrealMiddleCutCaptureEnabled' => 'boolean',
            'unrealMiddleCutHeight' => 'integer',
            'unrealResolution' => 'integer',
            'unrealStyles' => 'array',
        ];
    }

    public function getDTO(): StepsDTO
    {
        $dto = new StepsDTO();

        $styles = Styles::create($this->get('unrealStyles'));
        $resolution = Resolution::create($this->get('unrealResolution'));
        $middleCutHeight = MiddleCutHeight::create($this->get('unrealMiddleCutHeight'));
        $topDownViewCount = TopDownViewCount::create($this->get('unrealTopDownViewCount'));

        $dto
            ->setSteps($this->get('steps'))
            ->setIsNeuralLogEnabled($this->get('isNeuralLogEnabled', false))
            ->setIsUnrealPanoramasCaptureEnabled($this->get('isUnrealPanoramasCaptureEnabled', false))
            ->setIsUnrealTopViewsCaptureEnabled($this->get('isUnrealTopViewsCaptureEnabled', false))
            ->setIsUnrealMiddleCutCaptureEnabled($this->get('isUnrealTopViewsCaptureEnabled', false))
            ->setUnrealTopDownViewCount($topDownViewCount)
            ->setUnrealMiddleCutHeight($middleCutHeight)
            ->setUnrealResolution($resolution)
            ->setUnrealStyles($styles);

        if ($this->hasFile('plan')) {
            $dto->setPlan($this->file('plan'));
        }

        if ($this->hasFile('mask')) {
            $dto->setMask($this->file('mask'));
        }

        if ($this->hasFile('json')) {
            $dto->setJson($this->file('json'));
        }

        return $dto;
    }
}
