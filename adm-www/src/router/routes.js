const routes = [
  {
    path: '/login',
    component: () => import('layouts/lytlogin.vue'),
    children: [
      { path: '/', name: 'login.usuario', meta: { authempresa1: true }, component: () => import('pages/login/login-user.vue') },
      { path: 'google/callback', name: 'login.usuario2', meta: { authempresa1: true }, component: () => import('pages/login/login-user.vue') },
      { path: '/resetpwd/request', name: 'login.resetpwd.request', meta: { authempresa1: true }, component: () => import('pages/login/resetpwd-request.vue') },
      { path: '/resetpwd/change', name: 'login.resetpwd.change', meta: { authempresa1: true }, component: () => import('pages/login/resetpwd-codecheck.vue') },
      { path: 'usuario/logout', name: 'logout.usuario', component: () => import('pages/login/logoff-user.vue') }
    ]
  },
  {
    path: '/p',
    component: () => import('layouts/lytpublico.vue'),
    children: [
      { path: 'notas/empresa/xmlpendente', name: 'publico.coletas.xmlpendente', component: () => import('pages/publico/coletas/xmlpendente.vue') },
      { path: 'rastreabilidade', name: 'publico.carga.rastreabilidade', component: () => import('pages/publico/carga/rastreabilidade.vue') },
      { path: 'palete', name: 'publico.palete.consulta', component: () => import('pages/publico/palete/consulta.vue') }
    ]
  },
  {
    path: '/',
    component: () => import('layouts/lytdefault.vue'),
    meta: { authusuario: true },
    children: [
      { name: 'home', path: '/home', component: () => import('pages/home.vue') },
      { name: 'tabelaibpt', path: '/tabelaibpt', component: () => import('pages/tabelaibpt/index.vue'), meta: { permissao: 'tabelaibpt.index' } },
      { name: 'tabelaibpt.log', path: '/tabelaibpt/log', component: () => import('pages/tabelaibpt/log-download.vue'), meta: { permissao: 'tabelaibpt.index' } },
      { name: 'statusgeral', path: '/statusgeral', component: () => import('pages/home.vue'), meta: { permissao: 'comercial.home.consulta' } },
      { name: 'coletas.consulta', path: '/coletas', props: true, component: () => import('pages/operacional/coletas/consulta.vue') },

      { name: 'rastrear', path: '/entregas/rastrear/:chave', props: true, component: () => import('pages/operacional/carga/rastrear.vue') },
      { name: 'entregas.consulta', path: '/entregas', props: true, component: () => import('pages/operacional/coletasnotas/consulta.vue') },
      { name: 'entregas.consulta.rapida', path: '/', props: true, component: () => import('pages/operacional/carga/rastrear-consulta.vue') },

      { name: 'followup.dashboard1', path: '/dashboard', props: true, component: () => import('pages/comercial/followup/dashboard1.vue') },
      { name: 'followup.consulta', path: '/followup', props: true, component: () => import('pages/comercial/followup/consulta.vue') },

      { name: 'followup.usuario', path: '/usuario/', props: true, component: () => import('pages/cadastro/clienteusuarios/consulta.vue') },
      { name: 'comercial.nfe.view', path: '/comercial/nfe', component: () => import('pages/comercial/nfe/view.vue'), meta: { permissao: 'comercial.nfe.consultaporchave' } },
      { name: 'comercial.nfe.consultadetalhe', path: '/comercial/nfe/consulta', component: () => import('pages/comercial/nfe/consulta.vue'), meta: { permissao: 'comercial.nfe.consulta' } },

      { name: 'usuario.sempermissao', path: '/usuario/acessonegado', component: () => import('pages/AcessoNegado.vue') },

      { name: 'cadastros', path: '/cadastros', component: () => import('pages/cadastro/index.vue') },

      { name: 'usuario.meuperfil', path: '/usuario/meuperfil', component: () => import('pages/cadastro/usuarios/meuperfil.vue') }
    ]
  },
  {
    path: '/',
    component: () => import('layouts/lytdefault-notoolbar.vue'),
    meta: { authusuario: true },
    children: [
      { name: 'operacional.coletas.coletas.print', path: 'operacional/coletas/print', component: () => import('pages/operacional/coletas/printshow.vue') }
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
