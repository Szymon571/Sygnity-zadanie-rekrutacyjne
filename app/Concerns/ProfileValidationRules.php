<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait ProfileValidationRules
{
    /**
     * Get the validation rules used to validate user profiles.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function profileRules(?int $userId = null): array
    {
        return [
            'first_name' => $this->firstNameRules(),
            'last_name' => $this->lastNameRules(),
            'nickname' => $this->nicknameRules($userId),
            'email' => $this->emailRules($userId),
        ];
    }

    /**
     * Get the validation rules used to validate user first names.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function firstNameRules(): array
    {
        return ['required', 'string', 'max:100'];
    }

    /**
     * Get the validation rules used to validate user last names.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function lastNameRules(): array
    {
        return ['required', 'string', 'max:100'];
    }

    /**
     * Get the validation rules used to validate user nicknames.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function nicknameRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            'min:3',
            'max:50',
            'regex:/^[A-Za-z0-9._-]+$/',
            $userId === null
                ? Rule::unique(User::class, 'nickname')
                : Rule::unique(User::class, 'nickname')->ignore($userId),
        ];
    }

    /**
     * Get the validation rules used to validate user emails.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function emailRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            $userId === null
                ? Rule::unique(User::class)
                : Rule::unique(User::class)->ignore($userId),
        ];
    }
}
