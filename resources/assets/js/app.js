
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.$ = window.JQuery = require('jquery');
window.Vue = require('vue');
//window.axios = require('axios');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
Vue.component('notification-component', require('./components/Notification.vue'));
Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('servicio-component', require('./components/Servicio.vue'));
Vue.component('cliente-component', require('./components/Cliente.vue'));
Vue.component('proveedor-component', require('./components/Proveedor.vue'));
Vue.component('rol-component', require('./components/Rol.vue'));
Vue.component('user-component', require('./components/User.vue'));
Vue.component('lavado-component', require('./components/Lavado.vue'));
Vue.component('compra-component', require('./components/Compra.vue'));
Vue.component('venta-component', require('./components/Venta.vue'));
Vue.component('dashboard-component', require('./components/Dashboard.vue'));

const app = new Vue({
    el: '#app',
    data:{
        menu : 0,
        notifications:[]
    },
    created() {
        let me = this;     
        axios.post('notification/get').then(function(response) {
           //console.log(response.data);
           me.notifications=response.data;    
        }).catch(function(error) {
            console.log(error);
        });

        var userId = $('meta[name="userId"]').attr('content');
        
        Echo.private('App.User.' + userId).notification((notification) => {
             me.notifications.unshift(notification); 
        }); 
    }      
});
