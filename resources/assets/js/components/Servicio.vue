<template>
    <main class="main">
            <!-- Breadcrumb -->
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><strong><a style="color:#FFFFFF;" href="/">Home</a></strong></li>
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card scroll-box">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Servicios
                        <!--   Boton Nuevo    -->
                        <button type="button" @click="abrirModal('servicio','registrar')" class="btn btn-secondary">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                        <!---->
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <!--Criterios para el listado de busqueda -->
                                    <select class="form-control col-md-4" v-model="criterio">
                                      <option value="nombre">Servicio</option>
                                    </select>
                                    <input type="text" v-model="buscar" @keyup.enter="listarServicio(1,buscar,criterio)" class="form-control" placeholder="Texto a buscar">
                                    <button type="submit" @click="listarServicio(1,buscar,criterio)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                    <th>Servicio</th>
                                    <th>Descripcion</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="servicio in arrayServicio" :key="servicio.id">
                                    <td style="width:10%">
                                        <button type="button" @click="abrirModal('servicio','actualizar',servicio)" class="btn btn-warning btn-sm">
                                          <i class="icon-pencil"></i>
                                        </button> &nbsp;
                                        <button type="button" class="btn btn-danger btn-sm" @click="eliminarServicio(servicio)">
                                          <i class="icon-trash"></i>
                                        </button>
                                    </td>
                                    <td v-text="servicio.nombre"></td>
                                    <td v-text="servicio.descripcion" style="width:50%"></td>
                                    <td v-text="'$ '+servicio.precio"></td>
                                </tr>                               
                            </tbody>
                        </table>
                        <nav>
                            <!--Botones de paginacion -->
                            <ul class="pagination">
                                <li class="page-item" v-if="pagination.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar,criterio)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar,criterio)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar,criterio)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>
            <!--Inicio del modal agregar/actualizar-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar': modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Servicio</label>
                                    <div class="col-md-6">
                                        <input type="text" v-model="nombre" class="form-control" placeholder="Nombre del servicio">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Descipcion</label>
                                    <div class="col-md-9">
                                        <input type="text" v-model="descripcion" class="form-control" placeholder="Descripcion del servicio">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Precio</label>
                                    <div class="col-md-4">
                                        <input type="text" maxlength="8" v-on:keypress="isNumber(event)" v-model="precio" class="form-control" placeholder="Costo del servicio">
                                    </div>
                                </div>
                                <!-- Div para mostrar los errores que mande validerDepartamento -->
                                <div v-show="errorServicio" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjServicio" :key="error" v-text="error">

                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Botones del modal -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                            <!-- Condicion para elegir el boton a mostrar dependiendo de la accion solicitada-->
                            <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarServicio()">Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarServicio()">Actualizar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Fin del modal-->
            

        </main>
</template>

<!-- ************************************************************************************************************************************  -->
<!-- *********************************************************** CODIGO JAVASCRIPT *************************************************************************  -->
<!-- ************************************************************************************************************************************  -->

<script>
    export default {
        data(){
            return{
                id:0,
                nombre : '',
                descripcion : '',
                precio:0,
                arrayServicio : [],
                modal : 0,
                tituloModal : '',
                tipoAccion: 0,
                errorServicio : 0,
                errorMostrarMsjServicio : [],
                pagination : {
                    'total' : 0,         
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
                criterio : 'nombre', 
                buscar : ''
            }
        },
        computed:{
            isActived: function(){
                return this.pagination.current_page;
            },
            //Calcula los elementos de la paginación
            pagesNumber:function(){
                if(!this.pagination.to){
                    return [];
                }

                var from = this.pagination.current_page - this.offset;
                if(from < 1){
                    from = 1;
                }

                var to = from + (this.offset * 2);
                if(to >= this.pagination.last_page){
                    to = this.pagination.last_page;
                }

                var pagesArray = [];
                while(from <= to){
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
        },
        methods : {
            /**Metodo para mostrar los registros */
            listarServicio(page, buscar, criterio){
                let me = this;
                var url = '/servicio?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio;
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayServicio = respuesta.servicios.data;
                    me.pagination = respuesta.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            cambiarPagina(page, buscar, criterio){
                let me = this;
                //Actualiza la pagina actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esta pagina
                me.listarServicio(page,buscar,criterio);
            },
            /**Metodo para registrar  */
            registrarServicio(){
                if(this.validarServicio()) //Se verifica si hay un error (campo vacio)
                {
                    return;
                }

                let me = this;
                //Con axios se llama el metodo store de ServicioController
                axios.post('/servicio/registrar',{
                    'nombre': this.nombre,
                    'descripcion': this.descripcion,
                    'precio': this.precio
                }).then(function (response){
                    me.cerrarModal(); //al guardar el registro se cierra el modal
                    me.listarServicio(1,'','nombre'); //se enlistan nuevamente los registros
                    //Se muestra mensaje Success
                    swal({
                        position: 'top-end',
                        type: 'success',
                        title: 'Servicio agregado correctamente',
                        showConfirmButton: false,
                        timer: 1500
                        })
                }).catch(function (error){
                    console.log(error);
                });
            },
            actualizarServicio(){
                if(this.validarServicio()) //Se verifica si hay un error (campo vacio)
                {
                    return;
                }

                let me = this;
                //Con axios se llama el metodo update de ServicioController
                axios.put('/servicio/actualizar',{
                    'nombre': this.nombre,
                    'descripcion': this.descripcion,
                    'precio': this.precio,
                    'id' : this.id
                }).then(function (response){
                    me.cerrarModal();
                    me.listarServicio(1,'','nombre');
                    //window.alert("Cambios guardados correctamente");
                    swal({
                        position: 'top-end',
                        type: 'success',
                        title: 'Cambios guardados correctamente',
                        showConfirmButton: false,
                        timer: 1500
                        })
                }).catch(function (error){
                    console.log(error);
                });
            },
            eliminarServicio(data =[]){
                this.id=data['id'];
                this.nombre=data['nombre'];
                this.descripcion=data['descripcion'];
                //console.log(this.id);
                swal({
                title: '¿Desea eliminar?',
                text: "Esta acción no se puede revertir!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si, eliminar!'
                }).then((result) => {
                if (result.value) {
                    let me = this;

                axios.delete('/servicio/eliminar', 
                        {params: {'id': this.id}}).then(function (response){
                        swal(
                        'Borrado!',
                        'Departamento borrado correctamente.',
                        'success'
                        )
                        me.listarServicio(1,'','nombre');
                    }).catch(function (error){
                        console.log(error);
                    });
                }
                })
            },
            isNumber: function(evt) {
                evt = (evt) ? evt : window.event;
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
                    evt.preventDefault();;
                } else {
                    return true;
                }
            },
            validarServicio(){
                this.errorServicio=0;
                this.errorMostrarMsjServicio=[];

                if(!this.nombre) //Si la variable departamento esta vacia
                    this.errorMostrarMsjServicio.push("El nombre del servicio no puede ir vacio.");
                
                if(!this.precio) //Si la variable departamento esta vacia
                    this.errorMostrarMsjServicio.push("El precio del servicio no puede ir vacio.");

                if(this.errorMostrarMsjServicio.length)//Si el mensaje tiene almacenado algo en el array
                    this.errorServicio = 1;

                return this.errorServicio;
            },
            cerrarModal(){
                this.modal = 0;
                this.tituloModal = '';
                this.nombre = '';
                this.descripcion = '';
                this.precio=0;
                this.errorServicio = 0;
                this.errorMostrarMsjServicio = [];

            },
            /**Metodo para mostrar la ventana modal, dependiendo si es para actualizar o registrar */
            abrirModal(modelo, accion,data =[]){
                switch(modelo){
                    case "servicio":
                    {
                        switch(accion){
                            case 'registrar':
                            {
                                this.modal = 1;
                                this.tituloModal = 'Registrar Servicio';
                                this.nombre ='';
                                this.descripcion ='';
                                this.precio ='';
                                this.tipoAccion = 1;
                                break;
                            }
                            case 'actualizar':
                            {
                                //console.log(data);
                                this.modal =1;
                                this.tituloModal='Actualizar Servicio';
                                this.tipoAccion=2;
                                this.id=data['id'];
                                this.nombre=data['nombre'];
                                this.descripcion=data['descripcion'];
                                this.precio=data['precio'];
                                break;
                            }
                        }
                    }
                }
            }
        },
        mounted() {
            this.listarServicio(1,this.buscar,this.criterio);
        }
    }
</script>
<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .mostrar{
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }
    .div-error{
        display:flex;
        justify-content: center;
    }
    .text-error{
        color: red !important;
        font-weight: bold;
    }
    .scroll-box {
            overflow-x: scroll;
            width: auto;
            padding: 1rem
        }
</style>