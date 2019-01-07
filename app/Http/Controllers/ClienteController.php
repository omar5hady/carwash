<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Persona;

class ClienteController extends Controller
{
    public function index(Request $request){

        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if($buscar==''){
            $personas = Persona::orderBy('nombre','desc')->paginate(5);
        }
        else{
            $personas = Persona::where($criterio, 'like', '%'. $buscar . '%')->orderBy('nombre','desc')->paginate(5);
        }

        return [
            'pagination' => [
                'total'         => $personas->total(),
                'current_page'  => $personas->currentPage(),
                'per_page'      => $personas->perPage(),
                'last_page'     => $personas->lastPage(),
                'from'          => $personas->firstItem(),
                'to'            => $personas->lastItem(),
            ],
            'personas' => $personas
        ];

    }

    public function store(Request $request){
        $persona = new Persona();
        $persona->nombre = $request->nombre;
        $persona->rfc = $request->rfc;
        $persona->direccion = $request->direccion;
        $persona->telefono = $request->telefono;
        $persona->email = $request->email;
        $persona->save();

    }

    public function selectCiente(Request $request)
    {
        $personas = Persona::select('personas.id','personas.nombre')
            ->orderBy('nombre', 'asc')->get();
        return ['personas' => $personas];
    } 

    public function update(Request $request){
        $persona = Persona::findOrFail($request->id);
        $persona->nombre = $request->nombre;
        $persona->rfc = $request->rfc;
        $persona->direccion = $request->direccion;
        $persona->telefono = $request->telefono;
        $persona->email = $request->email;
        $persona->save();
    }

    public function destroy(Request $request)
    {
        //if(!$request->ajax())return redirect('/');
        $persona = Persona::findOrFail($request->id);
        $persona->delete();
    }
}
