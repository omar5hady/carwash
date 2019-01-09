<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $anio=date('Y');
        $ingresos=DB::table('compras as i')
        ->select(DB::raw('MONTH(i.fecha) as mes'),
        DB::raw('YEAR(i.fecha) as anio'),
        DB::raw('SUM(i.total) as total'))
        ->whereYear('i.fecha',$anio)
        ->groupBy(DB::raw('MONTH(i.fecha)'),DB::raw('YEAR(i.fecha)'))
        ->get();
 
        $ventas=DB::table('ventas as v')
        ->select(DB::raw('MONTH(v.fecha) as mes'),
        DB::raw('YEAR(v.fecha) as anio'),
        DB::raw('SUM(v.total) as total'))
        ->whereYear('v.fecha',$anio)
        ->groupBy(DB::raw('MONTH(v.fecha)'),DB::raw('YEAR(v.fecha)'))
        ->get();
 
        return ['ingresos'=>$ingresos,'ventas'=>$ventas,'anio'=>$anio];      
 
    }
}
