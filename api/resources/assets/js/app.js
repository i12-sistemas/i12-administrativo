import Vue from "vue";
import VueRouter from "vue-router";
import router from "./router";
// const EventBus = new Vue();
// Object.defineProperties(Vue.prototype, {
//   $bus: {
//     get: function() {
//       return EventBus;
//     }
//   }
// });

import moment from "moment";
import VueMomentJS from "vue-momentjs";
Vue.use(VueMomentJS, moment);

import axios from "axios";
import VueAxios from "vue-axios";
Vue.use(VueAxios, axios);
// axios.defaults.headers.common["X-CSRF-TOKEN"] = document
//   .querySelector('meta[name="csrf-token"]')
//   .getAttribute("content");
// axios.defaults.headers.common["user"] = document
//   .querySelector('meta[name="user"]')
//   .getAttribute("content");

import auth from "./auth.js";
import Lib from "./helpers/general-helpers.js";

// import VueSession from "vue-session";
// Vue.use(VueSession);

// import VueLocalStorage from "vue-localstorage";
// Vue.use(VueLocalStorage);

import pagination from "vuejs-uib-pagination";
Vue.use(pagination);

// import VueSessionStorage from "vue-sessionstorage";
// Vue.use(VueSessionStorage);

import VueClipboard from "vue-clipboard2";
Vue.use(VueClipboard);

// require("vue-flash-message/dist/vue-flash-message.min.css");
// import VueFlashMessage from "vue-flash-message";
// Vue.use(VueFlashMessage);

// import vSelect from "vue-select";
// Vue.component("v-select", vSelect);

// import ToggleButton from "vue-js-toggle-button";
// Vue.use(ToggleButton);

// import PrettyInput from "pretty-checkbox-vue/input";
// import PrettyCheck from "pretty-checkbox-vue/check";
// import PrettyRadio from "pretty-checkbox-vue/radio";

// Vue.component("p-input", PrettyInput);
// Vue.component("p-check", PrettyCheck);
// Vue.component("p-radio", PrettyRadio);
// require("pretty-checkbox/src/pretty-checkbox.scss");
// require("  i/css/materialdesignicons.min.css");

//https://zhanziyang.github.io/vue-croppa/#/quick-start
// import "vue-croppa/dist/vue-croppa.css";
// import Croppa from "vue-croppa";
// Vue.use(Croppa);

Vue.use(VueRouter);
Vue.use(Lib);

// //admin
// import AppAdmin from "./components/admin/App";
// import viewlogin from "./components/admin/views/login.vue";
// import viewconfiguracao from "./components/admin/views/configuracao.vue";
// import viewfinanceiro from "./components/admin/views/financeiro.vue";
// import viewi12gestao from "./components/admin/views/i12gestao.vue";
// import iPubIndex from "./components/admin/operacional/vendas/pdv/ipub/index.vue";

import AppPainelCliente from "./components/painelcliente/App";

const app = new Vue({
  el: "#app",
  router,
  components: { "app-painelcliente": AppPainelCliente },
  data: {
    logincliente: {
      ok: true,
      contato: null,
      cliente: null
    },
    adminlogged: false,

    user: null,
    base_url: base_url,
    url: url,
    csrf_token: null,
    origem: origem
  },
  created() {
    // this.user = document
    //   .querySelector('meta[name="user"]')
    //   .getAttribute("content");
    // if (this.user !== "") {
    //   this.user = JSON.parse(this.user);
    // }

    // get contato
    var contato = null;
    var metacontato = document.querySelector('meta[name="contato"]');
    if (metacontato) contato = metacontato.getAttribute("content");
    if (contato !== "") {
      this.logincliente.contato = JSON.parse(contato);
    }
    // get cliente/contato
    var cliente = null;
    var metacliente = document.querySelector('meta[name="cliente"]');
    if (metacliente) cliente = metacliente.getAttribute("content");
    if (cliente !== "") {
      this.logincliente.cliente = JSON.parse(cliente);
    }

    this.csrf_token = document
      .querySelector('meta[name="csrf-token"]')
      .getAttribute("content");

    this.checkadminlogin();
    this.checkclientelogin();
  },
  methods: {
    toolbarchanged: function() {
      this.$emit("toolbarchanged");
      console.log("toolbarchanged root");
    },
    checkclientelogin() {
      this.logincliente.ok = auth.loggedInPainelCliente();
    },
    checkadminlogin() {
      // this.adminlogged = !this.user.codusuario
      //   ? false
      //   : this.user.codusuario > 0;
    }
  }
});
