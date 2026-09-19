<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use Illuminate\Http\JsonResponse;

class PacienteController extends Controller
{
    public function index(): JsonResponse
    {
        $pacientes = Paciente::query()
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        return response()->json([
            'data' => $pacientes,
        ], 200);
    }
}
