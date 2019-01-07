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
                        <i class="fa fa-align-justify"></i> Facturas de compra
                        <!--   Boton Nuevo    -->
                        <button type="button" @click="abrirModal('compra','registrar')" class="btn btn-secondary">
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
                                      <option value="personas.nombre">Proveedor</option>
                                      <option value="compras.fecha">Fecha</option>
                                    </select>
                                    <input type="date" v-if="criterio=='compras.fecha'" v-model="buscar" @keyup.enter="listarCompra(1,buscar,criterio)" class="form-control" placeholder="Texto a buscar">
                                    <input type="text" v-else v-model="buscar" @keyup.enter="listarCompra(1,buscar,criterio)" class="form-control" placeholder="Texto a buscar">
                                    <button type="submit" @click="listarCompra(1,buscar,criterio)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                    <th>Proveedor</th>
                                    <th>RFC</th>
                                    <th>Fecha</th>
                                    <th>No. Factura</th>
                                    <th>Subtotal</th>
                                    <th>I.V.A.</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="compra in arrayCompra" :key="compra.id">
                                    <td style="width:10%">
                                        <button type="button" @click="abrirModal('compra','actualizar',compra)" class="btn btn-warning btn-sm">
                                          <i class="icon-pencil"></i>
                                        </button> &nbsp;
                                        <button type="button" class="btn btn-danger btn-sm" @click="eliminarCompra(compra)">
                                          <i class="icon-trash"></i>
                                        </button>
                                    </td>
                                    <td v-text="compra.nombre"></td>
                                    <td v-text="compra.rfc"></td>
                                    <td v-text="compra.fecha"></td>
                                    <td v-text="compra.num_factura"></td>
                                    <td v-text="'$ '+compra.sub_total"></td>
                                    <td v-text="'$ '+compra.iva"></td>
                                    <td v-text="'$ '+compra.total"></td>
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
                                    <label class="col-md-3 form-control-label" for="email-input">Proveedor (*)</label>
                                    <div class="col-md-9">
                                        <select class="form-control" v-model="persona_id">
                                            <option value="0">Seleccione el proveedor</option>
                                            <option v-for="proveedor in arrayProveedor" :key="proveedor.id" :value="proveedor.id" v-text="proveedor.nombre">
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">No. Factura (*)</label>
                                    <div class="col-md-9">
                                        <input type="text" v-model="num_factura" class="form-control" placeholder="Número de factura">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Fecha (*)</label>
                                    <div class="col-md-9">
                                        <input type="date" v-model="fecha" class="form-control" placeholder="Fecha">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Subtotal (*)</label>
                                    <div class="col-md-4">
                                        <input type="text" maxlength="8" v-on:keypress="isNumber(event)" v-model="sub_total" class="form-control" placeholder="Subtotal">
                                    </div>
                                </div>
                                <!-- Div para mostrar los errores que mande validerDepartamento -->
                                <div v-show="errorCompra" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjCompra" :key="error" v-text="error">

                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Botones del modal -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                            <!-- Condicion para elegir el boton a mostrar dependiendo de la accion solicitada-->
                            <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarCompra()">Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarCompra()">Actualizar</button>
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
                fecha : '',
                sub_total:0,
                persona_id: 0,
                num_factura:'',
                arrayProveedor : [],
                arrayCompra : [],
                modal : 0,
                tituloModal : '',
                tipoAccion: 0,
                errorCompra : 0,
                errorMostrarMsjCompra : [],
                pagination : {
                    'total' : 0,         
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
                criterio : 'personas.nombre', 
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
            listarCompra(page, buscar, criterio){
                let me = this;
                var url = '/compra?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio;
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayCompra = respuesta.compras.data;
                    me.pagination = respuesta.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            selectProveedor(){
                let me=this;
                var url= '/proveedor/selectProveedor';
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayProveedor = respuesta.personas;
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
                me.listarCompra(page,buscar,criterio);
            },
            /**Metodo para registrar  */
            registrarCompra(){
                if(this.validarCompra()) //Se verifica si hay un error (campo vacio)
                {
                    return;
                }

                let me = this;
                //Con axios se llama el metodo store de ServicioController
                axios.post('/compra/registrar',{
                    'persona_id': this.persona_id,
                    'fecha': this.fecha,
                    'sub_total': this.sub_total,
                    'num_factura': this.num_factura
                }).then(function (response){
                    me.cerrarModal(); //al guardar el registro se cierra el modal
                    me.listarCompra(1,'','personas.nombre'); //se enlistan nuevamente los registros
                    //Se muestra mensaje Success
                    swal({
                        position: 'top-end',
                        type: 'success',
                        title: 'Compra agregada correctamente',
                        showConfirmButton: false,
                        timer: 1500
                        })
                }).catch(function (error){
                    console.log(error);
                });
            },
            actualizarCompra(){
                if(this.validarCompra()) //Se verifica si hay un error (campo vacio)
                {
                    return;
                }

                let me = this;
                //Con axios se llama el metodo update de ServicioController
                axios.put('/compra/actualizar',{
                    'persona_id': this.persona_id,
                    'fecha': this.fecha,
                    'sub_total': this.sub_total,
                    'num_factura': this.num_factura,
                    'id' : this.id
                }).then(function (response){
                    me.cerrarModal();
                    me.listarCompra(1,'','personas.nombre');
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
            eliminarCompra(data =[]){
                this.id=data['id'];
                this.persona_id=data['persona_id'];
                this.fecha=data['fecha'];
                this.sub_total=data['sub_total'];
                this.num_factura=data['num_factura'];
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

                axios.delete('/compra/eliminar', 
                        {params: {'id': this.id}}).then(function (response){
                        swal(
                        'Borrado!',
                        'Registro borrado correctamente.',
                        'success'
                        )
                        me.listarCompra(1,'','personas.nombre');
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
            validarCompra(){
                this.errorCompra=0;
                this.errorMostrarMsjCompra=[];
                
                if(this.personal_id==0) 
                    this.errorMostrarMsjCompra.push("Se debe seleccionar el proveedor.");

                if(!this.sub_total) 
                    this.errorMostrarMsjCompra.push("El subtotal no puede ir vacio.");

                if(!this.fecha) 
                    this.errorMostrarMsjCompra.push("La fecha no puede ir vacia");

                if(!this.num_factura) 
                    this.errorMostrarMsjCompra.push("El numero de la factura no puede ir vacio");

                if(this.errorMostrarMsjCompra.length)//Si el mensaje tiene almacenado algo en el array
                    this.errorCompra = 1;

                return this.errorCompra;
            },
            cerrarModal(){
                this.modal = 0;
                this.tituloModal = '';
                this.persona_id = 0;
                this.fecha = '';
                this.num_factura = '';
                this.sub_total=0;
                this.errorCompra = 0;
                this.errorMostrarMsjCompra = [];

            },
            /**Metodo para mostrar la ventana modal, dependiendo si es para actualizar o registrar */
            abrirModal(modelo, accion,data =[]){
                this.selectProveedor();
                switch(modelo){
                    case "compra":
                    {
                        switch(accion){
                            case 'registrar':
                            {
                                this.modal = 1;
                                this.tituloModal = 'Registrar Compra';
                                this.persona_id = 0;
                                this.fecha ='';
                                this.num_factura = '';
                                this.sub_total ='';
                                this.tipoAccion = 1;
                                break;
                            }
                            case 'actualizar':
                            {
                                //console.log(data);
                                this.modal =1;
                                this.tituloModal='Actualizar Compra';
                                this.tipoAccion=2;
                                this.id=data['id'];
                                this.persona_id=data['persona_id'];
                                this.fecha=data['fecha'];
                                this.num_factura=data['num_factura'];
                                this.sub_total=data['sub_total'];
                                break;
                            }
                        }
                    }
                }
            }
        },
        mounted() {
            this.listarCompra(1,this.buscar,this.criterio);
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