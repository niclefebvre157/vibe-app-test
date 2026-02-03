<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamInviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('invite', $this->route('team'));
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
        ];
    }
}
