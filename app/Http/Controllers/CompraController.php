<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Compra;

class CompraController extends Controller
{
    public function index(Request $request){

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if ($buscar==''){
            $compras = Compra::join('personas','compras.persona_id','=','personas.id')
            ->select('compras.id','compras.fecha','compras.num_factura','compras.sub_total',
                'compras.iva','compras.total','personas.nombre','personas.rfc','compras.persona_id')
            ->orderBy('compras.fecha', 'desc')->paginate(3);
        }
        else{
            $compras = Compra::join('personas','compras.persona_id','=','personas.id')
            ->select('compras.id','compras.fecha','compras.num_factura','compras.sub_total',
                'compras.iva','compras.total','personas.nombre','personas.rfc','compras.persona_id')           
            ->where($criterio, 'like', '%'. $buscar . '%')
            ->orderBy('compras.fecha', 'desc')->paginate(3);
        }

        return [
            'pagination' => [
                'total'         => $compras->total(),
                'current_page'  => $compras->currentPage(),
                'per_page'      => $compras->perPage(),
                'last_page'     => $compras->lastPage(),
                'from'          => $compras->firstItem(),
                'to'            => $compras->lastItem(),
            ],
            'compras' => $compras
        ];

    }

    public function store(Request $request){
        $iva = $request->sub_total * 0.16;
        $total = $request->sub_total + $iva;

        $Compra = new Compra();
        $Compra->persona_id = $request->persona_id;
        $Compra->num_factura = $request->num_factura;
        $Compra->sub_total = $request->sub_total;
        $Compra->iva = $iva;
        $Compra->total = $total;
        $Compra->fecha = $request->fecha;
        $Compra->save();
    }

    public function update(Request $request){
        $iva = $request->sub_total * 0.16;
        $total = $request->sub_total + $iva;

        $Compra = Compra::findOrFail($request->id);
        $Compra->persona_id = $request->persona_id;
        $Compra->num_factura = $request->num_factura;
        $Compra->sub_total = $request->sub_total;
        $Compra->iva = $iva;
        $Compra->total = $total;
        $Compra->fecha = $request->fecha;
        $Compra->save();
    }

    public function destroy(Request $request)
    {
        if(!$request->ajax())return redirect('/');
        $Compra = Compra::findOrFail($request->id);
        $Compra->delete();
    }
}
