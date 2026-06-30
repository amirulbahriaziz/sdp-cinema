<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowtimeIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Public read endpoint — no auth required.
        return true;
    }

    public function rules(): array
    {
        return [
            'movie_id' => ['sometimes', 'integer', 'exists:movies,id'],
            'cinema_id' => ['sometimes', 'integer', 'exists:cinemas,id'],
            'hall_id' => ['sometimes', 'integer', 'exists:halls,id'],
            'tier_id' => ['sometimes', 'integer', 'exists:price_tiers,id'],
            'date' => ['sometimes', 'date_format:Y-m-d'],
            'from' => ['sometimes', 'date'],
            'to' => ['sometimes', 'date'],
        ];
    }
}
