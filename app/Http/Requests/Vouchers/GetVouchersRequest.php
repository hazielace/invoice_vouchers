<?php

namespace App\Http\Requests\Vouchers;

use Illuminate\Foundation\Http\FormRequest;

class GetVouchersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'serie' => 'nullable|string|max:150', 
            'number' => 'nullable|string|max:150',
            'start_date' => 'nullable|date_format:Y-m-d', 
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'paginate' => 'nullable|integer|min:1',
        ];
    }

    /**
     * Personalizar los mensajes de error.
     */
    public function messages(): array
    {
        return [
            'serie.string' => 'La serie debe ser una cadena de caracteres.',
            'number.string' => 'El número debe ser una cadena de caracteres.',
            'start_date.date_format' => 'La fecha de inicio debe estar en el formato YYYY-MM-DD.',
            'end_date.date_format' => 'La fecha de fin debe estar en el formato YYYY-MM-DD.',
            'end_date.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            'page.integer' => 'La página debe ser un número entero.',
            'paginate.integer' => 'El número de elementos por página debe ser un número entero.',
        ];
    }
}
