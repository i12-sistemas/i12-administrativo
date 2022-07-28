
import dataLoading from 'src/components/data-loading'
// import dataEmpty from 'src/components/data-empty'
// import uploader from 'src/components/uploader'
// import breadcrumbs from 'src/components/breadcrumbs'
// import LottieAnimation from 'lottie-web-vue'
import lottieinternal from 'src/components/lottie-internal.vue'
import cpnselectclientedefault from 'src/components/cnp-select-cliente.vue'

export default async ({ Vue }) => {
  // Vue.component('lottie-animation', LottieAnimation)
  Vue.component('lottieinternal', lottieinternal)
  Vue.component('my-select-clientes', cpnselectclientedefault)
  Vue.component('data-loading', dataLoading)
  // Vue.component('data-empty', dataEmpty)
  // Vue.component('uploader', uploader)
  // Vue.component('breadcrumbs', breadcrumbs)
}
