<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores'; // se referencia a que tabla pertenece el modelo
    protected $fillable = [
        'id','contacto','tel_contacto'];//asignacion en masa, definir las columnas de la tabla a la que se les mandaran valores
    
    public $timestamps = false;

    public function persona(){
        return $this->belongsTo('App\Persona');
    }
}
