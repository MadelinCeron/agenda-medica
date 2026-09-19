<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'paciente_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:pacientes,id',
            ],

            'doctor_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:doctores,id',
            ],

            'fecha_inicio' => [
                'sometimes',
                'required',
                'date',
            ],

            'fecha_fin' => [
                'sometimes',
                'required',
                'date',
            ],

            'motivo' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'estado' => [
                'sometimes',
                'required',
                'in:pendiente,confirmada,cancelada,atendida',
            ],
        ];
    }
}
