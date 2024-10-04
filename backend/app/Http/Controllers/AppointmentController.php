<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Veterinarian;
use Illuminate\Http\Request;
use Exception;

class AppointmentController extends Controller
{
    // Listar todas as marcações
    public function index()
    {
        try {
            $appointments = Appointment::with('veterinarian', 'user')->get();
            return response()->json([
                'success' => true,
                'data' => $appointments,
                'message' => 'Appointments retrieved successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve appointments.',
            ], 500);
        }
    }

    // Criar uma nova marcação
    public function store(Request $request)
    {
        if (is_array($request->period)) {
            $request->merge([
                'period' => $request->period['id']
            ]);
        }

        try {
            // Validar os dados recebidos
            $validated = $request->validate([
                'person_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'animal_name' => 'required|string|max:255',
                'animal_type' => 'required|string|max:255',
                'age' => 'required|integer|min:0',
                'symptoms' => 'required|string',
                'appointment_date' => 'required|date',
                'period' => 'required|in:morning,afternoon',
                'veterinarian_id' => 'nullable|exists:veterinarians,id',
            ]);

            // Associar o agendamento ao usuário logado
            $validated['user_id'] = auth()->id();

            // Criar o agendamento
            $appointment = Appointment::create($validated);

            return response()->json([
                'success' => true,
                'data' => $appointment,
                'message' => 'Appointment created successfully.'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create appointment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Exibir uma marcação específica
    public function show(Appointment $appointment)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $appointment->load('veterinarian', 'user'),
                'message' => 'Appointment retrieved successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve appointment.',
            ], 500);
        }
    }

    // Atualizar uma marcação existente
    public function update(Request $request, Appointment $appointment)
    {
        if (is_array($request->period)) {
            $request->merge([
                'period' => $request->period['id']
            ]);
        }

        try {
            // Validar os dados recebidos
            $validated = $request->validate([
                'person_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'animal_name' => 'required|string|max:255',
                'animal_type' => 'required|string|max:255',
                'age' => 'required|integer|min:0',
                'symptoms' => 'required|string',
                'appointment_date' => 'required|date',
                'period' => 'required|in:morning,afternoon',
                'veterinarian_id' => 'nullable|exists:veterinarians,id',
            ]);

            // Atualizar a marcação
            $appointment->update($validated);

            return response()->json([
                'success' => true,
                'data' => $appointment,
                'message' => 'Appointment updated successfully.'
            ], 200);

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

    public function assignVeterinarian(Request $request, Appointment $appointment)
    {
        try {
            // Validar apenas o campo veterinarian_id
            $validated = $request->validate([
                'veterinarian_id' => 'nullable|exists:veterinarians,id',
            ]);

            // Atualizar o veterinarian_id na marcação
            $appointment->update([
                'veterinarian_id' => $validated['veterinarian_id']
            ]);

            return response()->json([
                'success' => true,
                'data' => $appointment,
                'message' => 'Veterinarian assigned successfully.'
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
                'message' => 'Failed to assign veterinarian.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    //filtro para marcações atribuídas ao médico
    public function getAppointmentsByVeterinarian(Request $request)
    {
        $user = auth()->user();

        // Verifica se o modelo User tem uma relação com Veterinarian
        // Caso contrário, ajuste conforme a estrutura do seu projeto
        $veterinarian = Veterinarian::where('user_id', $user->id)->first();

        if (!$veterinarian) {
            return response()->json([
                'success' => false,
                'message' => 'Veterinarian not found for the authenticated user.',
            ], 404);
        }

        $query = Appointment::where('veterinarian_id', $veterinarian->id);

        if ($request->has('appointment_date')) {
            $query->where('appointment_date', $request->appointment_date);
        }

        if ($request->has('animal_type')) {
            $query->where('animal_type', $request->animal_type);
        }

        $appointments = $query->with('user')->get();

        return response()->json([
            'success' => true,
            'data' => $appointments,
            'message' => 'Appointments retrieved successfully.'
        ]);
    }

    public function myAppointments()
    {
        try {
            $user = auth()->user();

            $appointments = Appointment::where('user_id', $user->id)->get();

            return response()->json([
                'success' => true,
                'data' => $appointments,
                'message' => 'Appointments retrieved successfully.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve appointments.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getFilteredAppointments(Request $request)
    {
        $user = auth()->user();

        // Encontrar o veterinário logado com base no user_id
        $veterinarian = Veterinarian::where('user_id', $user->id)->first();

        if (!$veterinarian) {
            return response()->json([
                'success' => false,
                'message' => 'Veterinarian not found for the authenticated user.',
            ], 404);
        }

        // Iniciar a query para buscar as marcações atribuídas ao veterinário
        $query = Appointment::where('veterinarian_id', $veterinarian->id);

        // Filtrar por data da consulta se fornecido
        if ($request->has('appointment_date') && $request->appointment_date != '') {
            $query->where('appointment_date', $request->appointment_date);
        }

        // Filtrar por tipo de animal se fornecido
        if ($request->has('animal_type') && $request->animal_type != '') {
            $query->where('animal_type', 'LIKE', '%' . $request->animal_type . '%');
        }

        // Executar a consulta e retornar os resultados
        $appointments = $query->with('user')->get();

        return response()->json([
            'success' => true,
            'data' => $appointments,
            'message' => 'Filtered appointments retrieved successfully.'
        ]);
    }



    // Deletar uma marcação
    public function destroy(Appointment $appointment)
    {
        try {
            $appointment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Appointment deleted successfully.'
            ], 204);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete appointment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
