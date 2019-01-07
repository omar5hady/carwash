<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lavado extends Model
{
    protected $table = 'lavados'; // se referencia a que tabla pertenece el modelo
    protected $primaryKey = 'id'; //Referenciar la llave primaria
    protected $fillable = [
        'fecha','servicio_id','descripcion','importe'];//asignacion en masa, definir las columnas de la tabla a la que se les mandaran valores

    
    public function servicio(){
        return $this->belongsTo('App\Servicio');
    }
}
