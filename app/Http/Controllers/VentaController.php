<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Venta;

class VentaController extends Controller
{
    public function index(Request $request){

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if ($buscar==''){
            $ventas = Venta::join('personas','ventas.persona_id','=','personas.id')
            ->select('ventas.id','ventas.fecha','ventas.num_factura','ventas.sub_total',
                'ventas.iva','ventas.total','personas.nombre','personas.rfc','ventas.persona_id')
            ->orderBy('ventas.fecha', 'desc')->paginate(3);
        }
        else{
            $ventas = Venta::join('personas','ventas.persona_id','=','personas.id')
            ->select('ventas.id','ventas.fecha','ventas.num_factura','ventas.sub_total',
                'ventas.iva','ventas.total','personas.nombre','personas.rfc','ventas.persona_id')           
            ->where($criterio, 'like', '%'. $buscar . '%')
            ->orderBy('ventas.fecha', 'desc')->paginate(3);
        }

        return [
            'pagination' => [
                'total'         => $ventas->total(),
                'current_page'  => $ventas->currentPage(),
                'per_page'      => $ventas->perPage(),
                'last_page'     => $ventas->lastPage(),
                'from'          => $ventas->firstItem(),
                'to'            => $ventas->lastItem(),
            ],
            'ventas' => $ventas
        ];

    }

    public function store(Request $request){
        $iva = $request->sub_total * 0.16;
        $total = $request->sub_total + $iva;

        $Venta = new Venta();
        $Venta->persona_id = $request->persona_id;
        $Venta->num_factura = $request->num_factura;
        $Venta->sub_total = $request->sub_total;
        $Venta->iva = $iva;
        $Venta->total = $total;
        $Venta->fecha = $request->fecha;
        $Venta->save();
    }

    public function update(Request $request){
        $iva = $request->sub_total * 0.16;
        $total = $request->sub_total + $iva;

        $Venta = Venta::findOrFail($request->id);
        $Venta->persona_id = $request->persona_id;
        $Venta->num_factura = $request->num_factura;
        $Venta->sub_total = $request->sub_total;
        $Venta->iva = $iva;
        $Venta->total = $total;
        $Venta->fecha = $request->fecha;
        $Venta->save();
    }

    public function destroy(Request $request)
    {
        if(!$request->ajax())return redirect('/');
        $Venta = Venta::findOrFail($request->id);
        $Venta->delete();
    }
}
