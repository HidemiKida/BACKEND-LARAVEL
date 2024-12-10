<?php
namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Servicio;
use App\Models\TarjetaCredito;
use App\Models\Usuario;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompraController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero_tarjeta' => 'required|string|exists:tarjeta_creditos,numero_tarjeta',
            'fecha_expiracion' => 'required|date_format:Y-m-d', 
            'cvc' => 'required|string|digits:3',
            'servicio_id' => 'required|exists:servicios,servicio_id', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $usuario = JWTAuth::parseToken()->authenticate();
        
        // Verificar que el usuario tiene un role_id válido
        if (is_null($usuario->role_id)) {
            return response()->json(['error' => 'El usuario no tiene un rol asignado.'], 400);
        }

        $tarjeta = TarjetaCredito::where('numero_tarjeta', $request->numero_tarjeta)->first();
        $servicio = Servicio::findOrFail($request->servicio_id);

        if ($tarjeta->saldo >= $servicio->costo) { 
            $compra = new Compra();
            $compra->usuario_id = $usuario->usuario_id;
            $compra->tarjeta_id = $tarjeta->tarjeta_id;
            $compra->monto = $servicio->costo;
            $compra->fecha_compra = now();
            $compra->save();
            
            // Buscar el rol con id = 2 (asegurate de que el rol exista)
            $rolAdmin = Role::find(2);
            if (!$rolAdmin) {
                return response()->json(['error' => 'El rol de administrador no existe en la base de datos.'], 400);
            }
            
            // Asignar el rol al usuario
            $usuario->role_id = $rolAdmin->role_id;
            $usuario->save();
            // Registrar en los logs
            \Log::info('Compra realizada del servicio ' . $servicio->servicio_id);

            return response()->json(['message' => 'Compra realizada con éxito', 'compra' => $compra], 201);
        } else {
            return response()->json(['message' => 'Saldo insuficiente'], 400);
        }
    }
}
