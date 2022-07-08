import Vue from "vue";
import VueRouter from "vue-router";
import auth from "./auth.js";

import viewpainellogin from "./components/painelcliente/views/login.vue";
import viewpaineltrocarempresa from "./components/painelcliente/views/trocarempresa.vue";
import viewpainelhome from "./components/painelcliente/views/home.vue";
import viewpainelchamados from "./components/painelcliente/views/chamados.vue";
import viewpainelchamadonovo from "./components/painelcliente/views/chamadonovo.vue";
import viewpainelchamado from "./components/painelcliente/views/chamado.vue";
import viewpainelchamadopublico from "./components/painelcliente/views/chamadopublico.vue";

const routes = [
  {
    path: "/painelcliente",
    name: "painelcliente.home",
    component: viewpainelhome,
    beforeEnter: requireAuthCliente
  },
  {
    path: "/painelcliente/trocarempresa",
    name: "painelcliente.trocarempresa",
    component: viewpaineltrocarempresa,
    beforeEnter: requireAuthCliente
  },
  {
    path: "/painelcliente/chamados",
    name: "painelcliente.chamados",
    component: viewpainelchamados,
    beforeEnter: requireAuthCliente
  },
  {
    path: "/painelcliente/chamados/novo",
    name: "painelcliente.chamados.novo",
    component: viewpainelchamadonovo,
    beforeEnter: requireAuthCliente
  },

  {
    path: "/painelcliente/chamado/show/:idbase64/:idhashmd5",
    name: "painelcliente.chamado.show",
    component: viewpainelchamado,
    beforeEnter: requireAuthCliente
  },
  {
    path:
      "/painelcliente/chamado/show/publico/:idbase64/:idhashmd5/:email/:emailmd5",
    name: "painelcliente.chamado.show.publico",
    component: viewpainelchamadopublico
    // beforeEnter: requireAuthCliente
  },

  {
    path: "/painelcliente/login",
    component: viewpainellogin,
    name: "painelcliente.login",
    beforeEnter: checkLoginCliente
  }
];

function requireAuthCliente(to, from, next) {
  if (!auth.loggedInPainelCliente()) {
    next({ name: "painelcliente.login", query: { redirect: to.fullPath } });
  } else {
    next();
  }
}

function checkLoginCliente(to, from, next) {
  if (auth.loggedInPainelCliente()) {
    next({ path: "painelcliente/home" });
  } else {
    next();
  }
}

export default new VueRouter({
  mode: "history",
  routes: routes
});
