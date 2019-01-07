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
                        <i class="fa fa-align-justify"></i> Bitacora Lavados
                        <!--   Boton Nuevo    -->
                        <button type="button" @click="abrirModal('lavado','registrar')" class="btn btn-secondary">
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
                                      <option value="servicios.nombre">Servicio</option>
                                      <option value="lavados.fecha">Fecha</option>
                                      <option value="lavados.importe">Importe</option>
                                      <option value="lavados.descripcion">Descripcion</option>
                                    </select>
                                    <input type="date" v-if="criterio=='lavados.fecha'" v-model="buscar" @keyup.enter="listarLavado(1,buscar,criterio)" class="form-control" placeholder="Texto a buscar">
                                    <input type="text" v-else v-model="buscar" @keyup.enter="listarLavado(1,buscar,criterio)" class="form-control" placeholder="Texto a buscar">
                                    <button type="submit" @click="listarLavado(1,buscar,criterio)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                    <th>Servicio</th>
                                    <th>Descripcion</th>
                                    <th>Importe</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="lavado in arrayLavado" :key="lavado.id">
                                    <td style="width:10%">
                                        <button type="button" @click="abrirModal('lavado','actualizar',lavado)" class="btn btn-warning btn-sm">
                                          <i class="icon-pencil"></i>
                                        </button> &nbsp;
                                        <button type="button" class="btn btn-danger btn-sm" @click="eliminarLavado(lavado)">
                                          <i class="icon-trash"></i>
                                        </button>
                                    </td>
                                    <td v-text="lavado.nombre"></td>
                                    <td v-text="lavado.descripcion" style="width:40%"></td>
                                    <td v-text="'$ '+lavado.importe"></td>
                                    <td v-text="lavado.fecha"></td>
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
                                    <label class="col-md-3 form-control-label" for="email-input">Servicio (*)</label>
                                    <div class="col-md-9">
                                        <select class="form-control" @click="selectPrecioServicio(servicio_id)" v-model="servicio_id">
                                            <option value="0">Seleccione el servicio</option>
                                            <option v-for="servicio in arrayServicio" :key="servicio.id" :value="servicio.id" v-text="servicio.nombre + '| $'+ servicio.precio">
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Descripcion</label>
                                    <div class="col-md-9">
                                        <input type="text" v-model="descripcion" class="form-control" placeholder="Descripcion del servicio">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Importe</label>
                                    <div class="col-md-4">
                                        <input type="text" maxlength="8" v-on:keypress="isNumber(event)" v-model="importe" class="form-control" placeholder="Importe del servicio">
                                    </div>
                                </div>
                                <!-- Div para mostrar los errores que mande validerDepartamento -->
                                <div v-show="errorLavado" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjLavado" :key="error" v-text="error">

                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Botones del modal -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                            <!-- Condicion para elegir el boton a mostrar dependiendo de la accion solicitada-->
                            <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarLavado()">Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarLavado()">Actualizar</button>
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
                descripcion : '',
                importe:0,
                servicio_id: 0,
                arrayLavado : [],
                arrayServicio : [],
                modal : 0,
                tituloModal : '',
                tipoAccion: 0,
                errorLavado : 0,
                errorMostrarMsjLavado : [],
                pagination : {
                    'total' : 0,         
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
                criterio : 'servicios.nombre', 
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
            listarLavado(page, buscar, criterio){
                let me = this;
                var url = '/lavado?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio;
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayLavado = respuesta.lavados.data;
                    me.pagination = respuesta.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            selectServicio(){
                let me=this;
                var url= '/servicio/selectServicio';
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayServicio = respuesta.servicios;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },

            selectPrecioServicio(buscar){
                let me = this;
              
                me.arrayPrecios=[];
                var url = '/servicio/selectPrecioServicio?buscar=' + buscar;
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayPrecios = respuesta.precios;

                    /*if(me.importe==0)*/
                    me.importe = me.arrayPrecios[0].precio;
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
                me.listarLavado(page,buscar,criterio);
            },
            /**Metodo para registrar  */
            registrarLavado(){
                if(this.validarLavado()) //Se verifica si hay un error (campo vacio)
                {
                    return;
                }

                let me = this;
                //Con axios se llama el metodo store de ServicioController
                axios.post('/lavado/registrar',{
                    'servicio_id': this.servicio_id,
                    'descripcion': this.descripcion,
                    'importe': this.importe
                }).then(function (response){
                    me.cerrarModal(); //al guardar el registro se cierra el modal
                    me.listarLavado(1,'','servicios.nombre'); //se enlistan nuevamente los registros
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
            actualizarLavado(){
                if(this.validarLavado()) //Se verifica si hay un error (campo vacio)
                {
                    return;
                }

                let me = this;
                //Con axios se llama el metodo update de ServicioController
                axios.put('/lavado/actualizar',{
                    'servicio_id': this.servicio_id,
                    'descripcion': this.descripcion,
                    'importe': this.importe,
                    'id' : this.id
                }).then(function (response){
                    me.cerrarModal();
                    me.listarLavado(1,'','servicios.nombre');
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
            eliminarLavado(data =[]){
                this.id=data['id'];
                this.servicio_id=data['servicio_id'];
                this.descripcion=data['descripcion'];
                this.importe=data['importe'];
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

                axios.delete('/lavado/eliminar', 
                        {params: {'id': this.id}}).then(function (response){
                        swal(
                        'Borrado!',
                        'Registro borrado correctamente.',
                        'success'
                        )
                        me.listarLavado(1,'','servicios.nombre');
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
            validarLavado(){
                this.errorLavado=0;
                this.errorMostrarMsjLavado=[];
                
                if(!this.importe) //Si la variable departamento esta vacia
                    this.errorMostrarMsjLavado.push("El importe del servicio no puede ir vacio.");

                 if(this.servicio_id==0) //Si la variable departamento esta vacia
                    this.errorMostrarMsjLavado.push("Se debe seleccionar un servicio");

                if(this.errorMostrarMsjLavado.length)//Si el mensaje tiene almacenado algo en el array
                    this.errorLavado = 1;

                return this.errorLavado;
            },
            cerrarModal(){
                this.modal = 0;
                this.tituloModal = '';
                this.servicio_id = 0;
                this.descripcion = '';
                this.importe=0;
                this.errorLavado = 0;
                this.errorMostrarMsjLavado = [];

            },
            /**Metodo para mostrar la ventana modal, dependiendo si es para actualizar o registrar */
            abrirModal(modelo, accion,data =[]){
                this.selectServicio();
                switch(modelo){
                    case "lavado":
                    {
                        switch(accion){
                            case 'registrar':
                            {
                                this.modal = 1;
                                this.tituloModal = 'Registrar Servicio';
                                this.servicio_id = 0;
                                this.descripcion ='';
                                this.importe ='';
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
                                this.servicio_id=data['servicio_id'];
                                this.descripcion=data['descripcion'];
                                this.importe=data['importe'];
                                break;
                            }
                        }
                    }
                }
            }
        },
        mounted() {
            this.listarLavado(1,this.buscar,this.criterio);
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