<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    /**
     * Mostrar todas las reservas.
     */
    public function index()
    {
        $reservas = Reserva::with(['usuario', 'mesa'])->get();
        return response()->json($reservas, 200);
    }

    /**
     * Mostrar una reserva específica.
     */
    public function show($reserva_id)
    {
        $reserva = Reserva::with(['usuario', 'mesa'])->find($reserva_id);

        if (!$reserva) {
            return response()->json(['message' => 'Reserva no encontrada'], 404);
        }

        return response()->json($reserva, 200);
    }

   
    /**
     * Actualizar una reserva.
     */
    public function update(Request $request, $reserva_id)
    {
        $reserva = Reserva::find($reserva_id);

        if (!$reserva) {
            return response()->json(['message' => 'Reserva no encontrada'], 404);
        }

        $request->validate([
            'usuario_id' => 'exists:usuarios,usuario_id',
            'mesa_id' => 'exists:mesa,mesa_id',
            'fecha_reserva' => 'date|after:today',
            'estado' => 'in:pendiente,confirmada,cancelada',
        ]);

        $reserva->update($request->only(['usuario_id', 'mesa_id', 'fecha_reserva', 'estado']));

        return response()->json([
            'message' => 'Reserva actualizada exitosamente',
            'reserva' => $reserva
        ], 200);
    }

    /**
     * Eliminar una reserva.
     */
    public function destroy($reserva_id)
    {
        $reserva = Reserva::find($reserva_id);

        if (!$reserva) {
            return response()->json(['message' => 'Reserva no encontrada'], 404);
        }

        $reserva->delete();

        return response()->json(['message' => 'Reserva eliminada exitosamente'], 200);
    }

    /**
     * Cambiar el estado de una reserva.
     */
    public function cambiarEstado(Request $request, $reserva_id)
    {
        $reserva = Reserva::find($reserva_id);

        if (!$reserva) {
            return response()->json(['message' => 'Reserva no encontrada'], 404);
        }

        $request->validate([
            'estado' => 'required|in:pendiente,confirmada,cancelada',
        ]);

        $reserva->estado = $request->estado;
        $reserva->save();

        return response()->json([
            'message' => 'Estado de la reserva actualizado',
            'reserva' => $reserva
        ], 200);
    }
}
