<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Servicio;

class ServicioController extends Controller
{
    public function index(Request $request){

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if($buscar==''){
            $servicios = Servicio::orderBy('id','desc')->paginate(5);
        }
        else{
            $servicios = Servicio::where($criterio, 'like', '%'. $buscar . '%')->orderBy('id','desc')->paginate(5);
        }

        return [
            'pagination' => [
                'total'         => $servicios->total(),
                'current_page'  => $servicios->currentPage(),
                'per_page'      => $servicios->perPage(),
                'last_page'     => $servicios->lastPage(),
                'from'          => $servicios->firstItem(),
                'to'            => $servicios->lastItem(),
            ],
            'servicios' => $servicios
        ];

    }

    public function selectServicio(Request $request)
    {
        $servicios = Servicio::select('id','nombre','precio','descripcion')
        ->orderBy('nombre', 'asc')->get();
 
        return ['servicios' => $servicios];
    } 

    public function selectPrecioServicio(Request $request){
        //condicion Ajax que evita ingresar a la vista sin pasar por la opcion correspondiente del menu
       // if(!$request->ajax())return redirect('/');

        $buscar = $request->buscar;
        $precios = Servicio::select('id','precio')
        ->where('id', '=', $buscar )->get();

        return ['precios' => $precios];
    }

    public function store(Request $request){
        $servicio = new Servicio();
        $servicio->nombre = $request->nombre;
        $servicio->descripcion = $request->descripcion;
        $servicio->precio = $request->precio;
        $servicio->save();

    }

    public function update(Request $request){
        $servicio = Servicio::findOrFail($request->id);
        $servicio->nombre = $request->nombre;
        $servicio->descripcion = $request->descripcion;
        $servicio->precio = $request->precio;
        $servicio->save();
    }

    public function destroy(Request $request)
    {
        //if(!$request->ajax())return redirect('/');
        $servicio = Servicio::findOrFail($request->id);
        $servicio->delete();
    }
}
