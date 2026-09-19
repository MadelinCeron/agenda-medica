<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCitaRequest;
use App\Http\Requests\UpdateCitaRequest;
use App\Http\Requests\UpdateEstadoCitaRequest;
use App\Models\Cita;
use App\Services\CitaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class CitaController extends Controller
{
    public function __construct(
        private CitaService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filtros = $request->only([
            'doctor_id',
            'paciente_id',
            'desde',
            'hasta',
        ]);

        return response()->json([
            'data' => $this->service->listar($filtros),
        ], 200);
    }

    public function store(StoreCitaRequest $request): JsonResponse
    {
        try {
            $cita = $this->service->crear($request->validated());

            return response()->json([
                'message' => 'Cita creada correctamente.',
                'data' => $cita->load(['paciente', 'doctor']),
            ], 201);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 409);
        }
    }

    public function show(Cita $cita): JsonResponse
    {
        return response()->json([
            'data' => $cita->load(['paciente', 'doctor']),
        ], 200);
    }

    public function update(
        UpdateCitaRequest $request,
        Cita $cita
    ): JsonResponse {
        try {
            $cita = $this->service->actualizar(
                $cita,
                $request->validated()
            );

            return response()->json([
                'message' => 'Cita actualizada correctamente.',
                'data' => $cita,
            ], 200);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 409);
        }
    }

    public function cambiarEstado(
        UpdateEstadoCitaRequest $request,
        Cita $cita
    ): JsonResponse {
        $cita = $this->service->cambiarEstado(
            $cita,
            $request->validated()['estado']
        );

        return response()->json([
            'message' => 'Estado de la cita actualizado correctamente.',
            'data' => $cita,
        ], 200);
    }
}
