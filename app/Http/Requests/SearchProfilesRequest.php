<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SearchProfilesRequest extends FormRequest
{
    /**
     * جست‌وجو فقط داخل گروه مسیرهای احراز هویت‌شده اجرا می‌شود.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'city' => [
                'nullable',
                'string',
                'max:100',
            ],
            'min_age' => [
                'nullable',
                'integer',
                'between:18,100',
            ],
            'max_age' => [
                'nullable',
                'integer',
                'between:18,100',
            ],
            'marital_status' => [
                'nullable',
                'string',
                Rule::in([
                    'single',
                    'married',
                    'divorced',
                    'widowed',
                ]),
            ],
            'interested_in' => [
                'nullable',
                'string',
                'max:100',
                Rule::in([
                    'sport',
                    'travel',
                    'books',
                    'party',
                ]),
            ],
            'has_photo' => [
                'nullable',
                'boolean',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
            'page' => [
                'nullable',
                'integer',
                'min:1',
                'max:1000',
            ],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (
                    $this->filled('min_age') &&
                    $this->filled('max_age') &&
                    (int) $this->input('max_age') <
                        (int) $this->input('min_age')
                ) {
                    $validator->errors()->add(
                        'max_age',
                        'حداکثر سن باید بزرگ‌تر یا مساوی حداقل سن باشد.'
                    );
                }
            },
        ];
    }
}
