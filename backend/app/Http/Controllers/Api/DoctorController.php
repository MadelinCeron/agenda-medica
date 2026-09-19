<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;

class DoctorController extends Controller
{
    public function index(): JsonResponse
    {
        $doctores = Doctor::query()
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        return response()->json([
            'data' => $doctores,
        ], 200);
    }
}
