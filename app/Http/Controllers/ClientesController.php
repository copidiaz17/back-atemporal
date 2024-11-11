<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRecuest;
use App\Models\cliente;
use App\Models\User;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request\LoginRequest;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Number;
use PhpParser\Node\Stmt\TryCatch;

class ClientesController extends Controller
{
    public function index()
    {
        return view("formCliente");
    }

    public function crear(Request $request)
    {


        try {
            $cliente = new User();

            $data = $request->all();

            $cliente->type_id = 2;

            $cliente->name    = $request->input('cliente_nombre');
            $cliente->direccion = $request->input('cliente_direccion');
            $cliente->localidad = $request->input('cliente_localidad');
            $cliente->email     = $request->input('cliente_email');

            $cliente->password  = $request->input('cliente_password');
            $cliente->telefono  = $request->input('cliente_telefono');
            $cliente->save();


            return response()->json(['status' => 'OK'], 200)
                ->cookie(
                    'atemporal_token',          // Nombre de la cookie
                    $cliente->createToken('accessToken')->plainTextToken,
                    60,
                    '/',
                    'localhost',
                    false,
                    true,
                    false,
                    'Lax'
                );
        } catch (\Throwable $th) {
            return response($th);
        }
    }
    public function ingresar()
    {
        return view("Login");
    }
    public function login(Request $request)
    {
        try {

            $cliente_email = $request->input('cliente_email');
            $cliente_password = $request->input('cliente_password');
            $auth = Auth::attempt([
                'email' => $cliente_email,
                'password' => $cliente_password,
            ]);

            if ($auth) {

                $id = Auth::user()->id;

                $cliente = User::find($id);

                $cliente->tokens()->delete();

                return response()->json(['status' => 'OK'], 200)
                    ->cookie(
                        'atemporal_token',          // Nombre de la cookie
                        $cliente->createToken('accessToken')->plainTextToken,
                        60,
                        '/',
                        'localhost',
                        false,
                        true,
                        false,
                        'Lax'
                    );
            }
        } catch (Exception $exception) {
            //throw $th;
            return response()->json([
                'error' => $exception
            ]);
        }
    }

    public function datos(Request $request)
    {

        $cliente = $request->user;

        return response()->json([
            'status'    => 'OK',
            'cliente'   => $cliente,
        ]);
    }


    public function logout(Request $request)
    {
        return response()->json(['status' => 'OK'], 200)
            ->withoutCookie('atemporal_token');
    }

    public function carrito(Request $request)
    {
        $user = $request->user;
        $productos = $request->input('productos');
        $venta = new Venta();
        $venta->venta_fecha = Carbon::now();
        $venta->cliente_id = $request->user->id;
        $venta->save();
        foreach ($productos as $producto) {

            $ventaDetalle = new VentaDetalle();
            $ventaDetalle->venta_id = $venta->id;
            $ventaDetalle->venta_cantidad = $producto['cantidad'];
            $ventaDetalle->producto_id = $producto['id'];
            $ventaDetalle->venta_precio = $producto['producto_precio'];
            $ventaDetalle->venta_total = $producto['producto_precio'] * $producto['cantidad'];
            $ventaDetalle->save();
        }
        $numero = '3855301127';
        $mensaje = "Hola, me gustaría que me prepares mi pedido.\n\nDetalles:\n\nListado de productos:\n";
        $sumaTotal = 0;

        // Agregamos cada precio al mensaje
        foreach ($venta->detalle as $detalle) {
            $mensaje .= "- $detalle->venta_cantidad " . $detalle->producto->producto_nombre . " - $" . Number::format($detalle->producto->producto_precio, 2) . " x unidad - $" . $detalle->venta_total . "\n";
            $sumaTotal += $detalle->venta_total;
        }

        $mensaje .= "Total estimado: $" . Number::format($sumaTotal, 2) . "\n";

        $mensaje .= "\nGracias!";
        $mensaje_url = rawurlencode($mensaje);


        $enlace_whatsapp = "https://wa.me/$numero?text=$mensaje_url";

        return response()->json($enlace_whatsapp, 200);
    }
}
