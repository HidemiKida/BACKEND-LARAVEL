<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use App\Models\Disponibilidad;
use Illuminate\Http\Request;

class MesaController extends Controller
{
    /**
     * Mostrar todas las mesas de un restaurante.
     */
    public function index()
    {
        $mesa = Mesa::with('disponibilidad')->get();
        return response()->json($mesa, 200);
    }

    /**
     * Crear una nueva mesa.
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero_mesa' => 'required|integer|unique:mesa,numero_mesa', // Cambia 'mesas' por 'mesa'
            'capacidad' => 'required|integer|min:1',
            'restaurante_id' => 'required|exists:restaurantes,restaurante_id',
        ]);
    
        $mesa = Mesa::create($request->all());
    
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
        ]);
    
        // Actualizar la mesa con los datos proporcionados
        $mesa->update($request->only(['numero_mesa', 'capacidad', 'restaurante_id'])); // Usar solo los campos que esperamos actualizar
    
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

    /**
     * Agregar disponibilidad a una mesa.
     */
    public function agregarDisponibilidad(Request $request, $mesa_id)
    {
        $mesa = Mesa::find($mesa_id);

        if (!$mesa) {
            return response()->json(['message' => 'Mesa no encontrada'], 404);
        }

        $request->validate([
            'fecha_disponible' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        $disponibilidad = Disponibilidad::create([
            'mesa_id' => $mesa_id,
            'fecha_disponible' => $request->fecha_disponible,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
        ]);

        return response()->json([
            'message' => 'Disponibilidad agregada exitosamente',
            'disponibilidad' => $disponibilidad
        ], 201);
    }

    /**
     * Eliminar disponibilidad de una mesa.
     */
    public function eliminarDisponibilidad($disponibilidad_id)
    {
        $disponibilidad = Disponibilidad::find($disponibilidad_id);

        if (!$disponibilidad) {
            return response()->json(['message' => 'Disponibilidad no encontrada'], 404);
        }

        $disponibilidad->delete();

        return response()->json(['message' => 'Disponibilidad eliminada exitosamente'], 200);
    }
}
