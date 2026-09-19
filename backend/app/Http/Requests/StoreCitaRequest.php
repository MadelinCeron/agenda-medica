<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'paciente_id' => [
                'required',
                'integer',
                'exists:pacientes,id',
            ],

            'doctor_id' => [
                'required',
                'integer',
                'exists:doctores,id',
            ],

            'fecha_inicio' => [
                'required',
                'date',
            ],

            'fecha_fin' => [
                'required',
                'date',
                'after:fecha_inicio',
            ],

            'motivo' => [
                'required',
                'string',
                'max:255',
            ],

            'estado' => [
                'nullable',
                'in:pendiente,confirmada,cancelada,atendida',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'paciente_id.required' => 'Debe seleccionar un paciente.',
            'paciente_id.exists' => 'El paciente seleccionado no existe.',

            'doctor_id.required' => 'Debe seleccionar un doctor.',
            'doctor_id.exists' => 'El doctor seleccionado no existe.',

            'fecha_inicio.required' => 'La fecha y hora de inicio son obligatorias.',
            'fecha_inicio.date' => 'La fecha de inicio no es válida.',

            'fecha_fin.required' => 'La fecha y hora de finalización son obligatorias.',
            'fecha_fin.date' => 'La fecha de finalización no es válida.',
            'fecha_fin.after' => 'La hora de finalización debe ser posterior a la hora de inicio.',

            'motivo.required' => 'El motivo de la cita es obligatorio.',

            'estado.in' => 'El estado de la cita no es válido.',
        ];
    }
}
