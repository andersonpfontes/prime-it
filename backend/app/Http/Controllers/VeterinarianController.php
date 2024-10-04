<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Veterinarian;
use Illuminate\Http\Request;
use Exception;

class VeterinarianController extends Controller
{
    // Listar todos os veterinários
    public function index()
    {
        try {
            $veterinarians = Veterinarian::all();
            return response()->json([
                'success' => true,
                'data' => $veterinarians,
                'message' => 'Veterinarians retrieved successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve veterinarians.',
            ], 500);
        }
    }

    // Criar um novo veterinário
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:veterinarians,email',
                'specialization' => 'nullable|string|max:255',
            ]);

            $veterinarian = Veterinarian::create($validated);

            return response()->json([
                'success' => true,
                'data' => $veterinarian,
                'message' => 'Veterinarian created successfully.'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create veterinarian.',
            ], 500);
        }
    }

    // Exibir um veterinário específico
    public function show(Veterinarian $veterinarian)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $veterinarian,
                'message' => 'Veterinarian retrieved successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve veterinarian.',
            ], 500);
        }
    }

    // Atualizar um veterinário existente
    public function update(Request $request, Appointment $appointment)
    {
        try {

            // Caso contrário, validação completa para atualização geral da marcação
            $validated = $request->validate([
                'person_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'animal_name' => 'required|string|max:255',
                'animal_type' => 'required|string|max:255',
                'age' => 'required|integer|min:0',
                'symptoms' => 'required|string',
                'appointment_date' => 'required|date',
                'period' => 'required|in:morning,afternoon',
            ]);

            // Atualizar todos os campos da marcação
            $appointment->update($validated);

            return response()->json([
                'success' => true,
                'data' => $appointment,
                'message' => 'Appointment updated successfully.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update appointment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Deletar um veterinário
    public function destroy(Veterinarian $veterinarian)
    {
        try {
            $veterinarian->delete();

            return response()->json([
                'success' => true,
                'message' => 'Veterinarian deleted successfully.'
            ], 204);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete veterinarian.',
            ], 500);
        }
    }
}
