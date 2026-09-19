<?php

namespace App\Services;

use App\Models\Cita;
use App\Repositories\CitaRepository;
use RuntimeException;

class CitaService
{
    public function __construct(
        private CitaRepository $repository
    ) {}

    public function listar(array $filtros = [])
    {
        return $this->repository->listar($filtros);
    }

    public function obtener(int $id): Cita
    {
        return $this->repository->buscar($id);
    }

    public function crear(array $datos): Cita
    {
        $this->validarConflicto($datos);

        return $this->repository->crear($datos);
    }

    public function actualizar(Cita $cita, array $datos): Cita
    {
        $datosCompletos = array_merge(
            $cita->only([
                'paciente_id',
                'doctor_id',
                'fecha_inicio',
                'fecha_fin',
                'motivo',
                'estado',
            ]),
            $datos
        );

        $this->validarConflicto($datosCompletos, $cita->id);

        return $this->repository->actualizar($cita, $datos);
    }

    public function cambiarEstado(Cita $cita, string $estado): Cita
    {
        return $this->repository->actualizar($cita, [
            'estado' => $estado,
        ]);
    }

    private function validarConflicto(
        array $datos,
        ?int $ignorarId = null
    ): void {
        $existeConflicto = $this->repository->existeConflicto(
            $datos['doctor_id'],
            $datos['fecha_inicio'],
            $datos['fecha_fin'],
            $ignorarId
        );

        if ($existeConflicto) {
            throw new RuntimeException(
                'El doctor ya tiene una cita activa en ese horario.'
            );
        }
    }
}
