<?php

namespace App\Http\Controllers;

use App\Models\Restaurante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestauranteController extends Controller
{
    // Mostrar todos los restaurantes
    public function index()
    {
        $restaurantes = Restaurante::all();
        return response()->json($restaurantes);
    }

    // Mostrar un restaurante específico
    public function show($id)
    {
        $restaurante = Restaurante::find($id);

        if (!$restaurante) {
            return response()->json(['message' => 'Restaurante no encontrado'], 404);
        }

        return response()->json($restaurante);
    }

    // Crear un nuevo restaurante (Roles 2 y 3)
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->role_id, [2, 3])) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 403);
        }

        $request->validate([
            'nombre_restaurante' => 'required|string|max:100',
        ]);

        $restaurante = Restaurante::create($request->only('nombre_restaurante'));

        // Asignar el restaurante al usuario si tiene el rol 2
        if ($user->role_id == 2) {
            $user->restaurante_id = $restaurante->restaurante_id;
            $user->save(); // Guardar el cambio en el usuario
        }

        return response()->json([
            'message' => 'Restaurante creado exitosamente',
            'restaurante' => $restaurante,
        ], 201);
    }

    // Actualizar un restaurante existente (Roles 2 y 3)
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!in_array($user->role_id, [2, 3])) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 403);
        }

        $restaurante = Restaurante::find($id);

        if (!$restaurante) {
            return response()->json(['message' => 'Restaurante no encontrado'], 404);
        }

        $request->validate([
            'nombre_restaurante' => 'required|string|max:100',
        ]);

        $restaurante->update($request->only('nombre_restaurante'));

        return response()->json([
            'message' => 'Restaurante actualizado exitosamente',
            'restaurante' => $restaurante,
        ]);
    }

    // Eliminar un restaurante (Roles 2 y 3)
    public function destroy($id)
    {
        $user = Auth::user();

        if (!in_array($user->role_id, [2, 3])) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 403);
        }

        $restaurante = Restaurante::find($id);

        if (!$restaurante) {
            return response()->json(['message' => 'Restaurante no encontrado'], 404);
        }

        $restaurante->delete();

        return response()->json(['message' => 'Restaurante eliminado exitosamente']);
    }
}
