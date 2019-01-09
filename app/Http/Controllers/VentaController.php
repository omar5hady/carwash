<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Venta;
use Excel;
use Carbon\Carbon;
use DB;

class VentaController extends Controller
{
    public function index(Request $request){

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if ($buscar==''){
            $ventas = Venta::join('personas','ventas.persona_id','=','personas.id')
            ->select('ventas.id','ventas.fecha','ventas.num_factura','ventas.sub_total',
                'ventas.iva','ventas.total','personas.nombre','personas.rfc','ventas.persona_id','ventas.cancelada')
            ->orderBy('ventas.fecha', 'desc')->paginate(3);
        }
        else{
            $ventas = Venta::join('personas','ventas.persona_id','=','personas.id')
            ->select('ventas.id','ventas.fecha','ventas.num_factura','ventas.sub_total',
                'ventas.iva','ventas.total','personas.nombre','personas.rfc','ventas.persona_id','ventas.cancelada')           
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
        $Venta->cancelada = '0';
        $Venta->save();
    }

    public function destroy(Request $request)
    {
        if(!$request->ajax())return redirect('/');
        $Venta = Venta::findOrFail($request->id);
        $Venta->delete();
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $Venta = Venta::findOrFail($request->id);
        $Venta->cancelada = '1';
        $Venta->sub_total = 0;
        $Venta->iva = 0;
        $Venta->total = 0;
        $Venta->save();
    }

    public function exportExcel(Request $request)
    {
        $ventas = Venta::join('personas','ventas.persona_id','=','personas.id')
            ->select('ventas.id','ventas.fecha','ventas.num_factura','ventas.sub_total',
                'ventas.iva','ventas.total','personas.nombre','personas.rfc','ventas.persona_id','ventas.cancelada',
                DB::raw('MONTH(ventas.fecha) month'))
            ->orderBy('ventas.num_factura', 'desc')
            ->whereMonth('ventas.fecha', $request->month)
            ->whereYear('ventas.fecha', $request->year)
            ->get();

            return Excel::create('resumen_ventas', function($excel) use ($ventas){
                $excel->sheet('ventas', function($sheet) use ($ventas){
                    switch($ventas[0]->month){
                        case '1':
                            $mes = "Enero";
                        break;
                        case '2':
                            $mes = "Febrero";
                        break;
                        case '3':
                            $mes = "Marzo";
                        break;
                        case '4':
                            $mes = "Abril";
                        break;
                        case '5':
                            $mes = "Mayo";
                        break;
                        case '6':
                            $mes = "Junio";
                        break;
                        case '7':
                            $mes = "Julio";
                        break;
                        case '8':
                            $mes = "Agosto";
                        break;
                        case '9':
                            $mes = "Septiembre";
                        break;
                        case '10':
                            $mes = "Octubre";
                        break;
                        case '11':
                            $mes = "Noviembre";
                        break;
                        case '12':
                            $mes = "Diciembre";
                        break;
                        default:
                            $mes = "";
                    }
                    //$sheet->fromArray($compra);
                    $sheet->row(1, [
                        'Facturas ', 'Mes de: '. $mes 
                    ]);
                    $sheet->row(2, [
                        'Cliente', 'RFC', 'No. Factura', 'Fecha', 'Subtotal', 'I.V.A.','Total'
                    ]);


                    $sheet->cells('A1:C1', function ($cells) {
                        $cells->setBackground('#052154');
                        $cells->setFontColor('#ffffff');
                        // Set font family
                        $cells->setFontFamily('Calibri');

                        // Set font size
                        $cells->setFontSize(14);

                        // Set font weight to bold
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                    });

                    $sheet->cells('A2:G2', function ($cells) {
                        $cells->setBackground('#052154');
                        $cells->setFontColor('#ffffff');
                        // Set font family
                        $cells->setFontFamily('Calibri');

                        // Set font size
                        $cells->setFontSize(13);

                        // Set font weight to bold
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                    });

                    
                    $cont=2;

                    foreach($ventas as $index => $venta) {
                        $cont++;
                        
                        if($venta->fecha){
                            setlocale(LC_TIME, 'es');
                            $tiempo = new Carbon($venta->fecha);
                            $venta->fecha = $tiempo->formatLocalized('%d de %B de %Y');
                            
                        }
                        if($venta->cancelada == 1)
                                $cancelada="Cancelada";
                            else
                                $cancelada="";
                        $sheet->row($index+3, [
                            $venta->nombre, 
                            $venta->rfc, 
                            $venta->num_factura, 
                            $venta->fecha, 
                            $venta->sub_total,
                            $venta->iva,
                            $venta->total,
                            $cancelada
                        ]);	
                    }
                    $num='A2:H' . $cont;
                    $sheet->setBorder($num, 'thin');
                });
            }
            
            )->download('xls');
      
    }
}
