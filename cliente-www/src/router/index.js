import Vue from 'vue'
import VueRouter from 'vue-router'

import routes from './routes'

Vue.use(VueRouter)

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation
 */

export default function (/* { store, ssrContext } */) {
  const Router = new VueRouter({
    scrollBehavior: () => ({ x: 0, y: 0 }),
    routes,

    // Leave these as is and change from quasar.conf.js instead!
    // quasar.conf.js -> build -> vueRouterMode
    // quasar.conf.js -> build -> publicPath
    mode: process.env.VUE_ROUTER_MODE,
    base: process.env.VUE_ROUTER_BASE
  })

  Router.afterEach((to, from) => {
    Router.app.$store.dispatch('historyroute/add', to)
  })

  Router.beforeEach((to, from, next) => {
    var usrlogged = false
    try {
      var usuario = Router.app.$store.state.usuario.usuario
      usrlogged = Router.app.$store.state.usuario.logado
    } catch (error) {
      console.error(error)
    }

    if (!(to.meta ? (to.meta ? to.meta.drawerr_allow : false) : false)) {
      Vue.prototype.$eventbus.$emit('drawerr_remove')
    }
    if (to.matched.some(record => record.meta.authusuario)) {
      if (!usrlogged) {
        next({ name: 'login.usuario', query: { redirect: to.path } })
      }
    }
    if (to.matched.some(record => record.meta.permissao === 'operador')) {
      // var idpermissao = ''
      // for (let index = 0; index < to.matched.length; index++) {
      //   const element = to.matched[index]
      //   if (element.meta) {
      //     if (element.meta.permissao) {
      //       idpermissao = element.meta.permissao
      //       break
      //     }
      //   }
      // }
      // var permissao = Router.app.$helpers.permite(idpermissao)
      if (!usuario.ehoperador) next({ name: 'usuario.sempermissao', query: { idpermissao: 'operador', redirect: to.fullPath } })
    }
    next()
  })

  return Router
}
