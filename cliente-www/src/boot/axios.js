import axios from 'axios'
import moment from 'moment'

import { Money } from 'v-money'

export default async ({ Vue, store }) => {
  moment.locale('pt-br')
  Vue.component('money', Money)
  Vue.prototype.$axios = axios.create({
    baseURL: Vue.prototype.$configini.API_URL,
    withCredentials: false,
    headers: {
      'Access-Control-Allow-Origin': '*',
      'Access-Control-Allow-Headers': 'Authorization',
      'Access-Control-Allow-Methods': 'GET, POST, OPTIONS, PUT, PATCH, DELETE',
      'Content-Type': 'application/json;charset=UTF-8'
    }
  })
  // Add a 401 response interceptor
  Vue.prototype.$axios.interceptors.response.use(function (response) {
    store.dispatch('usuario/reseterror401')
    return response
  }, function (error) {
    var httpcod = error.response ? error.response.status : null
    if (httpcod === 401) {
      store.dispatch('usuario/adderror401')
      return Promise.reject(error)
    } else {
      return Promise.reject(error)
    }
  })
}
