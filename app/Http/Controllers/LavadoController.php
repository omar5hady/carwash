<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lavado;
use App\Servicio;
use App\User;
use Carbon\Carbon;
use DB;
use App\Notification;
use App\Notifications\NotifyAdmin;

class LavadoController extends Controller
{
    public function index(Request $request){

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if ($buscar==''){
            $lavados = Lavado::join('servicios','lavados.servicio_id','=','servicios.id')
            ->select('lavados.id','lavados.fecha','lavados.importe','lavados.descripcion',
                'servicios.nombre','lavados.servicio_id')
            ->orderBy('lavados.id', 'desc')->paginate(8);
        }
        else{
            $lavados = Lavado::join('servicios','lavados.servicio_id','=','servicios.id')
            ->select('lavados.id','lavados.fecha','lavados.importe','lavados.descripcion', 'lavados.servicio_id',
                'servicios.nombre')            
            ->where($criterio, 'like', '%'. $buscar . '%')
            ->orderBy('lavados.id', 'desc')->paginate(8);
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

    public function listarPDF(Request $request, $fecha_ini, $fecha_fin){
        //$buscar = $request->fecha_ini;
        //$buscar2 = $request->fecha_fin;
        setlocale(LC_TIME, 'es');
        $lavados = Lavado::join('servicios','lavados.servicio_id','=','servicios.id')
            ->select('lavados.id','lavados.fecha','lavados.importe','lavados.descripcion',
                'servicios.nombre','lavados.servicio_id')
            ->whereBetween('lavados.fecha', [$fecha_ini, $fecha_fin])
            ->orderBy('lavados.id', 'desc')->get();

        $suma= Lavado::select(DB::raw("SUM(lavados.importe) as total"))
        ->whereBetween('lavados.fecha', [$fecha_ini,$fecha_fin])->get();

        /*return [
            'total' => $suma,
            'lavados' => $lavados
        ];*/

        $pdf = \PDF::loadView('pdf.lavadospdf',[ 'total' => $suma,'lavados' => $lavados]);
        return $pdf->download('lavados.pdf');
    }

    public function store(Request $request){
        $current = Carbon::today()->format('ymd');

        $lavado = new Lavado();
        $lavado->servicio_id = $request->servicio_id;
        $lavado->descripcion = $request->descripcion;
        $lavado->importe = $request->importe;
        $lavado->fecha = $current;
        $lavado->save();


        //Notificacion de un servicio
        $fechaActual= date('Y-m-d');
        $numLavados = DB::table('lavados')->whereDate('created_at', $fechaActual)->count();

        $arregloDatos = [
            'servicios' => [
                        'numero' => $numLavados,
                        'msj' => 'Servicios'
            ]
        ];
        $allUsers = User::all();

        foreach ($allUsers as $notificar) {
            User::findOrFail($notificar->id)->notify(new NotifyAdmin($arregloDatos)); 
        }

   
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
