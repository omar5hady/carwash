<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'servicios'; // se referencia a que tabla pertenece el modelo
    protected $primaryKey = 'id'; //Referenciar la llave primaria
    protected $fillable = [
        'nombre','descripcion','precio'];//asignacion en masa, definir las columnas de la tabla a la que se les mandaran valores

    public function lavados(){
        return $this->hasMany('App\Lavado');
    }
}
