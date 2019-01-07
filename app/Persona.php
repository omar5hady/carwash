<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'personas'; // se referencia a que tabla pertenece el modelo
    protected $primaryKey = 'id'; //Referenciar la llave primaria
    protected $fillable = [
        'nombre','rfc','direccion','telefono','email'];//asignacion en masa, definir las columnas de la tabla a la que se les mandaran valores

    
    public function proveedor(){
        return $this->hasOne('App\Proveedor');
    }

    public function user(){
        return $this->hasOne('App\User');
    }

    public function compras(){
        return $this->hasMany('App\Compra');
    }

    public function ventas(){
        return $this->hasMany('App\Venta');
    }

}
