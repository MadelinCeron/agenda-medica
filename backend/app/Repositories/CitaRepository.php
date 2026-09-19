<?php

namespace App\Repositories;

use App\Models\Cita;

class CitaRepository
{
    public function listar(array $filtros = [])
    {
        $query = Cita::with(['paciente', 'doctor']);

        if (!empty($filtros['doctor_id'])) {
            $query->where('doctor_id', $filtros['doctor_id']);
        }

        if (!empty($filtros['paciente_id'])) {
            $query->where('paciente_id', $filtros['paciente_id']);
        }

        if (!empty($filtros['desde'])) {
            $query->where('fecha_inicio', '>=', $filtros['desde']);
        }

        if (!empty($filtros['hasta'])) {
            $query->where('fecha_fin', '<=', $filtros['hasta']);
        }

        return $query
            ->orderBy('fecha_inicio')
            ->get();
    }

    public function buscar(int $id): Cita
    {
        return Cita::with(['paciente', 'doctor'])
            ->findOrFail($id);
    }

    public function crear(array $datos): Cita
    {
        return Cita::create($datos);
    }

    public function actualizar(Cita $cita, array $datos): Cita
    {
        $cita->update($datos);

        return $cita->fresh(['paciente', 'doctor']);
    }

    public function existeConflicto(
        int $doctorId,
        string $inicio,
        string $fin,
        ?int $ignorarId = null
    ): bool {
        return Cita::query()
            ->where('doctor_id', $doctorId)
            ->where('estado', '!=', 'cancelada')
            ->when(
                $ignorarId,
                fn ($query) => $query->where('id', '!=', $ignorarId)
            )
            ->where('fecha_inicio', '<', $fin)
            ->where('fecha_fin', '>', $inicio)
            ->exists();
    }
}
