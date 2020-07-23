<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class BlockEmail implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        return substr($value, strrpos($value, '@' )+1) != 'ukr.net';
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Почта этого сервиса запрещена к использованию';
    }
}
