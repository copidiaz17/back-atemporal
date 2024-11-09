<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRecuest;
use App\Models\cliente;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request\LoginRequest;
use Illuminate\Support\Facades\Cookie;
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
            //code...

            // $request->validate([
            //     'cliente_email' => 'required',
            //     'cliente_password' => 'required',
            // ]);

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

                return response()
                    ->json([
                        'status' => 'OK',
                        'token' => $cliente->createToken('accesToken')->plainTextToken,
                        'bruno' => 'Soy bruno un genio crack, idolo mundial'
                    ])
                    ->cookie('AtemporalCookie', $cliente->createToken('accessToken')->plainTextToken);
            } else {
                return response()->json([
                    'status' => 'KO'
                ]);
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
        // $cliente = User::find($id);

        return response()->json([
            'status'    => 'OK',
            'cliente'   => $cliente,
        ]);
    }


    public function logout(Request $request) {
        if ($request->hasCookie('atemporal_token')) {
            // Eliminar la cookie de la respuesta
            Cookie::queue(Cookie::forget('atemporal_token'));
        }
        return response([], 200);
    }
}
