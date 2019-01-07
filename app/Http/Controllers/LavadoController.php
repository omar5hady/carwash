<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lavado;
use App\Servicio;
use Carbon\Carbon;

class LavadoController extends Controller
{
    public function index(Request $request){

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if ($buscar==''){
            $lavados = Lavado::join('servicios','lavados.servicio_id','=','servicios.id')
            ->select('lavados.id','lavados.fecha','lavados.importe','lavados.descripcion',
                'servicios.nombre','lavados.servicio_id')
            ->orderBy('lavados.id', 'desc')->paginate(3);
        }
        else{
            $lavados = Lavado::join('servicios','lavados.servicio_id','=','servicios.id')
            ->select('lavados.id','lavados.fecha','lavados.importe','lavados.descripcion', 'lavados.servicio_id',
                'servicios.nombre')            
            ->where($criterio, 'like', '%'. $buscar . '%')
            ->orderBy('lavados.id', 'desc')->paginate(3);
        }

        return [
            'pagination' => [
                'total'         => $lavados->total(),
                'current_page'  => $lavados->currentPage(),
                'per_page'      => $lavados->perPage(),
                'last_page'     => $lavados->lastPage(),
                'from'          => $lavados->firstItem(),
                'to'            => $lavados->lastItem(),
            ],
            'lavados' => $lavados
        ];

    }

    public function store(Request $request){
        $current = Carbon::today()->format('ymd');

        $lavado = new Lavado();
        $lavado->servicio_id = $request->servicio_id;
        $lavado->descripcion = $request->descripcion;
        $lavado->importe = $request->importe;
        $lavado->fecha = $current;
        $lavado->save();
    }

    public function update(Request $request){
        $lavado = Lavado::findOrFail($request->id);
        $lavado->servicio_id = $request->servicio_id;
        $lavado->descripcion = $request->descripcion;
        $lavado->importe = $request->importe;
        $lavado->save();
    }

    public function destroy(Request $request)
    {
        if(!$request->ajax())return redirect('/');
        $lavado = Lavado::findOrFail($request->id);
        $lavado->delete();
    }
}
