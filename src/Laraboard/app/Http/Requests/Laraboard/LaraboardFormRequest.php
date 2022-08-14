<?php

namespace App\Http\Requests\Laraboard;

use Illuminate\Foundation\Http\FormRequest;

abstract class LaraboardFormRequest extends FormRequest
{
    /**
     * Set default values of parameter
     * - If the form request value is not defined.
     *
     * @return array|null
     */
    public function defaults(): ?array
    {
        return null;
    }

    /**
     * Prepare for validation
     * - If default value exists on the input field, define the default value.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // add default values
        $defaults = $this->defaults();
        if (!is_null($defaults)) {
            foreach ($this->defaults() as $key => $defaultValue) {
                if (!$this->has($key)) {
                    $this->merge([$key => $defaultValue]);
                }
            }
        }
    }
}
