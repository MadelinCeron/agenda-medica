<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $inicio = $this->input('fecha_inicio');
            $fin = $this->input('fecha_fin');

            if ($inicio && $fin && strtotime($fin) <= strtotime($inicio)) {
                $validator->errors()->add(
                    'fecha_fin',
                    'La hora de finalización debe ser posterior a la hora de inicio.'
                );
            }
        });
    }
}
