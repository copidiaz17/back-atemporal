<?php

    namespace App\Http\Controllers;

use App\Http\Requests\LoginRecuest;
use App\Models\cliente;
use App\Models\User;
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
           
            
        $cliente = new User();

       

        $cliente->type_id = 2;
       
        $cliente->nombre    = $request->input('cliente_nombre');
        $cliente->direccion = $request->input('cliente_direccion');
        $cliente->localidad = $request->input('cliente_localidad');
        $cliente->email     = $request->input('cliente_email');
        
        $cliente->password  = $request->input('cliente_password');
        $cliente->telefono  = $request->input('cliente_telefono');
        
        $cliente->save();

        return response()->json(['status'=>'OK'], 200);

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

        $auth = Auth::attempt([
            'email' => $cliente_email,
            'password' => $cliente_password,
        ]);

        if($auth){

            $id = Auth::user()->id;

            $cliente = User::find($id);
            
            $cliente->tokens()->delete();

            return response()
                ->cookie('atemporalCuki', $cliente->createToken('accesToken')->plainTextToken)
                ->json([
                    'status' => 'OK',
                    'token' => $cliente->createToken('accesToken')->plainTextToken,
                    'bruno' => 'Soy bruno un genio crack, idolo mundial'
                ]);
        }else{
            return response()->json([
                'status' => 'KO'
            ]);
        }
    }
       
    public function datos()
    {
           
        $id = Auth::user()->id;
        $cliente = User::find($id);
        
        return response()->json([
            'status'    => 'OK',
            'cliente'   => $cliente 
        ]);
        
    }

        
    }       