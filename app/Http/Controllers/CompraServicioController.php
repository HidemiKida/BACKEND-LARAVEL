<?php

namespace App\Http\Controllers;

use App\Models\TarjetaCredito;
use App\Models\Usuario;
use App\Models\CompraServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompraServicioController extends Controller
{
    /**
     * Realizar la compra del servicio por un cliente (rol 1).
     */
    public function comprarServicio(Request $request)
    {
        $user = Auth::user();

        if ($user->role_id != 1) {
            return response()->json(['message' => 'Solo los clientes pueden realizar esta acción'], 403);
        }

        $request->validate([
            'numero_tarjeta' => 'required|string|size:16',
            'fecha_expiracion' => 'required|date',
            'cvc' => 'required|string|size:3',
        ]);

        // Buscar la tarjeta de crédito
        $tarjeta = TarjetaCredito::where('numero_tarjeta', $request->numero_tarjeta)
            ->where('fecha_expiracion', $request->fecha_expiracion)
            ->where('cvc', $request->cvc)
            ->first();

        if (!$tarjeta) {
            return response()->json(['message' => 'Tarjeta de crédito no válida'], 404);
        }

        // Verificar saldo suficiente
        $costoServicio = 100.00; // Ejemplo de costo fijo
        if ($tarjeta->saldo < $costoServicio) {
            return response()->json(['message' => 'Saldo insuficiente en la tarjeta'], 400);
        }

        // Realizar la compra
        $tarjeta->saldo -= $costoServicio;
        $tarjeta->save();

        // Registrar la compra
        CompraServicio::create([
            'usuario_id' => $user->usuario_id,
            'tarjeta_id' => $tarjeta->tarjeta_id,
            'costo' => $costoServicio,
        ]);

        // Cambiar el rol del usuario a admin (rol 2)
        $user->role_id = 2;
        $user->save();

        return response()->json([
            'message' => 'Compra realizada con éxito. Ahora tienes acceso como administrador.',
            'nuevo_rol' => 'admin',
        ], 200);
    }

    /**
     * Ver historial de compras (solo superadmin).
     */
    public function historialCompras()
    {
        $user = Auth::user();

        if ($user->role_id != 3) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 403);
        }

        $compras = CompraServicio::with(['usuario', 'tarjeta'])->get();

        return response()->json($compras);
    }

    /**
     * Ver todos los usuarios (solo superadmin).
     */
    public function verUsuarios()
    {
        $user = Auth::user();

        if ($user->role_id != 3) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 403);
        }

        $usuarios = Usuario::all();

        return response()->json($usuarios);
    }

    /**
     * Gestionar costo del servicio (solo superadmin).
     */
    public function gestionarServicio(Request $request)
    {
        $user = Auth::user();

        if ($user->role_id != 3) {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 403);
        }

        $request->validate([
            'nuevo_costo' => 'required|numeric|min:1',
        ]);

        // Aquí podrías actualizar una configuración o tabla que guarde el costo del servicio
        $costoServicio = $request->nuevo_costo;

        return response()->json([
            'message' => 'Costo del servicio actualizado con éxito',
            'nuevo_costo' => $costoServicio,
        ]);
    }
}
