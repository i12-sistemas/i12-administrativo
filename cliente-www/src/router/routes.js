const routes = [
  {
    path: '/login',
    component: () => import('layouts/lytlogin.vue'),
    children: [
      { path: '/', name: 'login.usuario', meta: { authempresa1: true }, component: () => import('pages/login/login-user.vue') },
      { path: 'empresa', name: 'login.usuario.empresa', meta: { authempresa1: true }, component: () => import('pages/login/login-user-empresa.vue') },
      // { path: 'google/callback', name: 'login.usuario2', meta: { authempresa1: true }, component: () => import('pages/login/login-user.vue') },
      { path: '/resetpwd/request', name: 'login.resetpwd.request', meta: { authempresa1: true }, component: () => import('pages/login/resetpwd-request.vue') },
      { path: '/resetpwd/change', name: 'login.resetpwd.change', meta: { authempresa1: true }, component: () => import('pages/login/resetpwd-codecheck.vue') },
      { path: 'usuario/logout', name: 'logout.usuario', component: () => import('pages/login/logoff-user.vue') }
    ]
  },
  {
    path: '/public',
    component: () => import('layouts/lytpublico.vue'),
    children: [
      { path: 'validacao/:tipo', name: 'publico.coletas.xmlpendente', component: () => import('pages/publico/validacao/index.vue') }
    ]
  },
  {
    path: '/',
    component: () => import('layouts/lytdefault.vue'),
    meta: { authusuario: true },
    children: [
      { name: 'home', path: '/', component: () => import('pages/home.vue') },

      { name: 'usuario.sempermissao', path: '/usuario/acessonegado', component: () => import('pages/AcessoNegado.vue') },

      { name: 'atendimentos', path: '/atendimentos', props: true, component: () => import('pages/atendimentos/listagem.vue'), meta: { permissao: 'operador' } },
      { name: 'atendimentos.novo', path: '/atendimentos/novo', props: true, component: () => import('pages/atendimentos/novo.vue'), meta: { permissao: 'operador' } },
      { name: 'atendimentos.atendimento', path: '/atendimentos/atendimento/:id', props: true, component: () => import('pages/atendimentos/detalhe.vue'), meta: { permissao: 'operador' } },

      { name: 'usuario.meuperfil', path: '/usuario/meuperfil', component: () => import('pages/cadastro/usuarios/meuperfil.vue') }
    ]
  }
]

// Always leave this as last one
if (process.env.MODE !== 'ssr') {
  routes.push({
    path: '*',
    component: () => import('pages/Error404.vue')
  })
}

export default routes
