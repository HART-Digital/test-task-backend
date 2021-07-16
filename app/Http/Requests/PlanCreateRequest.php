<?php

namespace App\Http\Requests;

use App\DTO\PlanCreateDTO;
use App\VO\PlanOptions\MiddleCutHeight;
use App\VO\PlanOptions\Resolution;
use App\VO\PlanOptions\Styles;
use App\VO\PlanOptions\TopDownViewCount;
use Illuminate\Foundation\Http\FormRequest;

class PlanCreateRequest extends FormRequest
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
        $topDownViewCountMin = TopDownViewCount::MIN;
        $topDownViewCountMax = TopDownViewCount::MAX;

        $resolutionMin = Resolution::MIN;
        $resolutionMax = Resolution::MAX;

        $middleCutHeightMin = MiddleCutHeight::MIN;
        $middleCutHeightMax = MiddleCutHeight::MAX;

        return [
            'plan' => 'required|bail|image',
            'mask' => 'bail|image',
            'log' => 'boolean',
            'disable_panoramas' => 'boolean',
            'disable_top_views' => 'boolean',
            'disable_furniture' => 'boolean',
            'disable_unreal' => 'boolean',
            'disable_middle_cut' => 'boolean',
            'styles' => 'array',
            'styles.*' => 'integer|min:0',
            'resolution' => "integer|min:{$resolutionMin}|max:{$resolutionMax}",
            'top_down_view_count' => "integer|min:{$topDownViewCountMin}|max:{$topDownViewCountMax}",
            'middle_cut_height' => "integer|min:{$middleCutHeightMin}|max:{$middleCutHeightMax}",
        ];
    }

    public function getDTO()
    {
        return new PlanCreateDTO(
            $this->file('plan'),
            $this->file('mask'),
            $this->boolean('log'),
            $this->boolean('disable_panoramas'),
            $this->boolean('disable_top_views'),
            $this->boolean('disable_furniture'),
            $this->boolean('disable_unreal'),
            $this->boolean('disable_middle_cut'),
            TopDownViewCount::create($this->get('top_down_view_count')),
            Styles::create($this->get('styles')),
            Resolution::create($this->get('resolution')),
            MiddleCutHeight::create($this->get('middle_cut_height'))
        );
    }
}
