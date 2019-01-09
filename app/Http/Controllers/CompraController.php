<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Compra;
use Excel;
use Carbon\Carbon;
use DB;

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

    public function exportExcel(Request $request)
    {
        $mes="";
        switch($request->month){
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

        $compras = Compra::join('personas','compras.persona_id','=','personas.id')
            ->select('compras.id','compras.fecha','compras.num_factura','compras.sub_total',
                'compras.iva','compras.total','personas.nombre','personas.rfc','compras.persona_id',
                DB::raw('MONTH(compras.fecha) month'))
            ->orderBy('compras.fecha', 'desc')
            ->whereMonth('compras.fecha', $request->month)
            ->whereYear('compras.fecha', $request->year)
            ->get();

            return Excel::create('resumen_compras', function($excel) use ($compras){
                $excel->sheet('compras', function($sheet) use ($compras){

                    switch($compras[0]->month){
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
                        'Facturas ', 'Mes de: '.$mes 
                    ]);
                    $sheet->row(2, [
                        'Proveedor', 'RFC', 'No. Factura', 'Fecha', 'Subtotal', 'I.V.A.','Total'
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

                    foreach($compras as $index => $compra) {
                        $cont++;
                        
                        if($compra->fecha){
                            setlocale(LC_TIME, 'es');
                            $tiempo = new Carbon($compra->fecha);
                            $compra->fecha = $tiempo->formatLocalized('%d de %B de %Y');
                            
                        }
                        
                        $sheet->row($index+3, [
                            $compra->nombre, 
                            $compra->rfc, 
                            $compra->num_factura, 
                            $compra->fecha, 
                            $compra->sub_total,
                            $compra->iva,
                            $compra->total
                        ]);	
                    }
                    $num='A2:G' . $cont;
                    $sheet->setBorder($num, 'thin');
                });
            }
            
            )->download('xls');
      
    }

}
