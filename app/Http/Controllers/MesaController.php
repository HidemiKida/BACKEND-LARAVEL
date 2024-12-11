<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use App\Models\Disponibilidad;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class MesaController extends Controller
{
    /**
     * Mostrar todas las mesas de un restaurante.
     */
    public function index()
    {
         // Obtener el usuario autenticado
        $usuario = JWTAuth::parseToken()->authenticate();

        if (!$usuario->restaurante_id) {
            return response()->json(['error' => 'El usuario no tiene un restaurante asociado.'], 403);
        }
        
        $mesas = Mesa::with('disponibilidad')
                ->where('restaurante_id', $usuario->restaurante_id)
                ->get();

        return response()->json($mesas, 200);
    }

    /**
     * Crear una nueva mesa.
     */


    public function store(Request $request)
    {
         // Obtener el usuario autenticado
        $usuario = JWTAuth::parseToken()->authenticate();

        if (!$usuario->restaurante_id) {
            return response()->json(['error' => 'El usuario no tiene un restaurante asociado.'], 403);
        }

        $request->validate([
            'numero_mesa' => 'required|integer', // Cambia 'mesas' por 'mesa'
            'capacidad' => 'required|integer|min:1',
            'ubicacion' => 'required|string|max:255',
            'estado_mesa' => 'required|boolean'
        ]);
    
        $mesa = Mesa::create([
            'numero_mesa' => $request->numero_mesa,
            'capacidad' => $request->capacidad,
            'ubicacion' => $request->ubicacion,
            'estado_mesa'=> $request->estado_mesa,
            'restaurante_id' => $usuario->restaurante_id,
        ]);
    
        return response()->json([
            'message' => 'Mesa creada exitosamente',
            'mesa' => $mesa
        ], 201);
    }

    /**
     * Mostrar una mesa específica con su disponibilidad.
     */
    public function show($mesa_id)
    {
        $mesa = Mesa::with('disponibilidad')->find($mesa_id);

        if (!$mesa) {
            return response()->json(['message' => 'Mesa no encontrada'], 404);
        }

        return response()->json($mesa, 200);
    }

    /**
     * Actualizar una mesa.
     */
    public function update(Request $request, $mesa_id)
    {
        // Buscar la mesa por su 'mesa_id' (en lugar de 'id')
        $mesa = Mesa::find($mesa_id);
    
        // Verificar si la mesa existe
        if (!$mesa) {
            return response()->json(['message' => 'Mesa no encontrada'], 404);
        }
    
        // Validar los datos de la mesa, excepto el campo que se está actualizando
        $request->validate([
            'numero_mesa' => 'integer|unique:mesa,numero_mesa,' . $mesa->mesa_id . ',mesa_id',  // Corregido aquí, agregando 'mesa_id' como referencia
            'capacidad' => 'integer|min:1',
            'ubicacion' => 'required|string|max:255',
        ]);
    
        // Actualizar la mesa con los datos proporcionados
        $mesa->update($request->only(['numero_mesa', 'capacidad', 'ubicacion'])); // Usar solo los campos que esperamos actualizar
    
        // Devolver la respuesta con la mesa actualizada
        return response()->json([
            'message' => 'Mesa actualizada exitosamente',
            'mesa' => $mesa // Retornar la instancia actualizada
        ], 200);
    }
    
    /**
     * Eliminar una mesa.
     */
    public function destroy($mesa_id)
    {
        $mesa = Mesa::find($mesa_id);

        if (!$mesa) {
            return response()->json(['message' => 'Mesa no encontrada'], 404);
        }

        $mesa->delete();

        return response()->json(['message' => 'Mesa eliminada exitosamente'], 200);
    }

}