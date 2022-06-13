<?php

namespace OmidZahed\LaravelRequestDocs\Rules;

use Illuminate\Contracts\Validation\Rule;

class SwaggerDoc implements Rule
{
    private $description;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($description)
    {
        $this->description = $description;
    }


    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '';
    }

    public function __toString(): string
    {
        return $this->description;
    }
}
