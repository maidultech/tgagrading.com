<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

class RequiredWithoutCheckbox implements Rule
{
    protected $noGradeInputs;

    public function __construct($noGradeInputs)
    {
        $this->noGradeInputs = $noGradeInputs;
    }

    public function passes($attribute, $value)
    {
        // Extract keys from the attribute path like centering.81.0
        $keys = explode('.', $attribute);
        $detailKey = $keys[1] ?? null;
        $cardKey = $keys[2] ?? null;

        if (isset($this->noGradeInputs[$detailKey][$cardKey]) && $this->noGradeInputs[$detailKey][$cardKey] === 'on') {
            return true; // Skip validation if cert_no_grade is checked.
        }

        return $value !== null; // Ensure value is present if cert_no_grade is not checked.
    }

    public function message()
    {
        return 'This field is required unless the corresponding cert_no_grade is checked.';
    }
}
