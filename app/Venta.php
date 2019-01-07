<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas'; // se referencia a que tabla pertenece el modelo
    protected $primaryKey = 'id'; //Referenciar la llave primaria
    protected $fillable = [
        'fecha','persona_id','num_factura','sub_total','total'];//asignacion en masa, definir las columnas de la tabla a la que se les mandaran valores

    
    public function persona(){
        return $this->belongsTo('App\Persona');
    }
}
