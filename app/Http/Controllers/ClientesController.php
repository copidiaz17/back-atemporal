<?php

    namespace App\Http\Controllers;

use App\Http\Requests\LoginRecuest;
use App\Models\cliente;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Http\Request\LoginRequest;
    

    class ClientesController extends Controller
    {
        Public function index(){
        return view("formCliente");  
    }

        Public function crear(Request $request){
           
            
            $cliente = new Cliente();

            $cliente->cliente_nombre = $request->input('cliente_nombre');
            $cliente->cliente_direccion = $request->input('cliente_direccion');
            $cliente->cliente_localidad =$request->input('cliente_localidad');
            $cliente->cliente_email = $request->input('cliente_email');
            $cliente->cliente_contraseña = Hash::make($request->input('cliente_password'));
            $cliente->cliente_telefono = $request->input('cliente_telefono');
            

            $cliente->save();

            return response()->json(['status'=>'OK'],200);

        }

        public function ingresar(){
            return view("Login");
        }
        public function login(Request $request)
        {
            $request->validate([ 
                'cliente_email' => 'required',
                'cliente_password' => 'required', 
            ]);

            $cliente_email = $request->input('cliente_email');
            $cliente_password = $request->input('cliente_password');

            $auth = Auth::guard('cliente')->attempt([

                

                'cliente_email' => $cliente_email,
                'cliente_contraseña' => $cliente_password,


            ]);

           // return $auth;

            if($auth){

                //$id = Auth::guard('cliente')->id;

                $cliente = Cliente::where('email', $cliente_email)->first();

                return response()->json([
                    'status' => 'OK',
                    'token' => $cliente->createToken('accesToken')->plainTextToken
                ]);
            }else{
                return response()->json([
                    'status' => 'KO'
                ]);
            }
        }

        

        
    }       