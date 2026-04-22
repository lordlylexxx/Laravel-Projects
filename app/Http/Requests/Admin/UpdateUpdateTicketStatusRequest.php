<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUpdateTicketStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->role === 'admin';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', 'string', Rule::in(['resolve', 'reopen', 'unresolve'])],
            'resolution_notes' => ['required_if:action,resolve', 'nullable', 'string', 'max:10000'],
            'reopen_note' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
