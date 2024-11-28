<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\venta;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ProductosController extends Controller
{
    public function index(Request $request)
    {

        $productos = Producto::all();
        //return $productos;
        //para enviarlo a la vista
        //return view("Productos", compact("productos"));
         return response()->json($productos,200);
        // $tokens = PersonalAccessToken::all();
        // $user = $request->user;
        // return response()
        //     ->json([
        //         'user' => $request->user,
        //         'tokens' => $tokens, 
        //         'token' => $request->cookie('atemporal_token')
        //     ]);
    }

    public function crear()
    {
        return view("formProducto");
    }

    public function store(Request $request)
    {

        $producto = new Producto();

        $producto->producto_nombre = $request->nombre;
        $producto->producto_descripcion = $request->descripcion;
        $producto->producto_imagen = $request->imagen;
        $producto->producto_precio = $request->precio;

        $producto->save();

        $productoJSON = json_encode($producto);

        return $productoJSON;

        //return redirect('/Productos');

    }

    public function mostrar($producto)
    {
        $producto = Producto::find($producto);
        // return view("mostrar", compact("producto"));
        $productoJSON = json_encode($producto);

        return $productoJSON;
    }

    public function editar($id)
    {

        $producto = Producto::findOrFail($id);
        return view("formEdicionProducto", compact('producto'));
    }

    public function actualizar(Request $request, $producto)
    {
        $producto = Producto::find($producto);

        $producto->producto_nombre = $request->nombre;
        $producto->producto_descripcion = $request->descripcion;
        $producto->producto_imagen = $request->imagen;
        $producto->producto_precio = $request->precio;

        $producto->save();

        //return redirect("/Productos/{$producto->producto_id}");

        $productoJSON = json_encode($producto);

        return $productoJSON;
    }

    public function categoria()
    {
        // Obtener todas las categorías junto con el recuento de productos
        $categorias = Categoria::withCount('productos')->get();

        // Devolver la vista con las categorías
        return view('categorias', compact('categorias'));
    }

    public function prod_categoria($categoria)
    {
        // Obtener la categoría específica junto con sus productos
        $categoria = Categoria::where('categoria_nombre', 'like', $categoria)->with('productos')->get();
        $productos = $categoria->productos;

        // Devolver la vista con los productos de la categoría
        // return view('prodxCategoria', compact('categoria', 'productos'));
        return response()->json($productos);
    }


    public function eliminar($id)
    {

        $producto = Producto::findOrFail($id);
        $producto->delete();

        return redirect("/Productos");
    }
}
