<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;

class ClientesController extends Controller
{
    public function index()
    {
        return view("formCliente");
    }

    public function crear(Request $request)
    {
        $data = $request->validate([
            'cliente_nombre' => ['required', 'max:255'],
            'cliente_direccion' => ['required', 'max:255'],
            'cliente_localidad' => ['required', 'max:255'],
            'cliente_email' => ['required', 'max:255', 'email', 'unique:cliente,cliente_email'],
            'cliente_password' => ['required', 'max:255'],
            'cliente_telefono' => ['required', 'max:255'],
        ]);

        $cliente = new User();
        $cliente->type_id = 2;
        $cliente->name    = $data['cliente_nombre'];
        $cliente->direccion = $data['cliente_direccion'];
        $cliente->localidad = $data['cliente_localidad'];
        $cliente->email     = $data['cliente_email'];
        $cliente->password  = $data['cliente_password'];
        $cliente->telefono  = $data['cliente_telefono'];
        $cliente->save();

        Auth::login($cliente);

        return response()
            ->json(['status' => 'OK'], 200)
            ->cookie(
                'atemporal_token',          // Nombre de la cookie
                $cliente->createToken('accessToken')->plainTextToken,
                60,
                '/',
                config('session.domain'),
                false,
                true,
                false,
                'Lax'
            );
    }
    public function ingresar()
    {
        return view("Login");
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'cliente_email' => ['required', 'max:255', 'email'],
            'cliente_password' => ['required', 'max:255'],
        ]);

        $credentials = [
            'email' => $data['cliente_email'],
            'password' => $data['cliente_password'],
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            /** @var User $cliente */
            $cliente = Auth::user();

            return response()
                ->json(['status' => 'OK'], 200)
                ->cookie(
                    'atemporal_token',
                    $cliente->createToken('accessToken')->plainTextToken,
                    60,
                    '/',
                    config('session.domain'),
                    false,
                    true,
                    false,
                    'Lax'
                );
        }

        abort(422);
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
        return response()
            ->json(['status' => 'OK'], 200)
            ->withoutCookie('atemporal_token');
    }

    public function carrito(Request $request)
    {
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
        foreach ($venta->detalles as $detalle) {
            $mensaje .= "- $detalle->venta_cantidad " . $detalle->producto->producto_nombre . " - $" . Number::format($detalle->producto->producto_precio, 2) . " x unidad - $" . $detalle->venta_total . "\n";
            $sumaTotal += $detalle->venta_total;
        }

        $mensaje .= "Total estimado: $" . Number::format($sumaTotal, 2) . "\n";

        $mensaje .= "\nGracias!";
        $mensaje_url = rawurlencode($mensaje);


        $enlace_whatsapp = "https://wa.me/{$numero}?text={$mensaje_url}";

        return response()->json($enlace_whatsapp, 200);
    }
}
