@extends('principal')
@section('contenido')

@if(Auth::check())
    @if(Auth::user()->idrol == 1)
        <template v-if="menu==0">
            <dashboard-component></dashboard-component>
        </template>
        <template v-if="menu==1">
            <servicio-component></servicio-component>
        </template>
        <template v-if="menu==2">
            <cliente-component></cliente-component>
        </template>
        <template v-if="menu==3">
            <proveedor-component></proveedor-component>
        </template>
        <template v-if="menu==4">
            <lavado-component></lavado-component>
        </template>
        <template v-if="menu==5">
            <venta-component></venta-component>
        </template>
        <template v-if="menu==6">
            <compra-component></compra-component>
        </template>

        <template v-if="menu==7">
            <user-component></user-component>
        </template>
        <template v-if="menu==8">
            <rol-component></rol-component>
        </template>
    @elseif(Auth::user()->idrol == 2)
        <template v-if="menu==0">
            <dashboard-component></dashboard-component>
        </template>
        <template v-if="menu==1">
            <servicio-component></servicio-component>
        </template>
        <template v-if="menu==2">
            <cliente-component></cliente-component>
        </template>
        <template v-if="menu==3">
            <proveedor-component></proveedor-component>
        </template>
        <template v-if="menu==4">
            <lavado-component></lavado-component>
        </template>
        <template v-if="menu==5">
            <venta-component></venta-component>
        </template>
        <template v-if="menu==6">
            <compra-component></compra-component>
        </template>
    @else
    
    @endif
@endif


@endsection
        