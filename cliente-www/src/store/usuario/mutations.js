import Vue from 'vue'
import Contato from 'src/mvc/models/contato.js'
import Cliente from 'src/mvc/models/cliente.js'

export const setip = async (state, dados) => {
  state.ip = dados
}

export const setret = async (state, ret) => {
  state.ret = ret
}

export const adderror401 = async (state) => {
  state.axioserror401count = state.axioserror401count + 1
}
export const reseterror401 = async (state) => {
  state.axioserror401count = 0
}

export const setusuariologado = async (state, dados) => {
  try {
    var lUser = new Contato(dados.contato)
    var lCliente = new Cliente(dados.cliente)
    // var lUserObj = JSON.parse(JSON.stringify(lUser))
    state.usuario = lUser
    state.cliente = lCliente
    state.logado = true
    var instance = Vue.prototype.$axios
    instance.defaults.headers.common['x-auth-token'] = state.token
    instance.defaults.headers.common['x-auth-accesscode'] = state.accesscode
    instance.defaults.headers.common['x-auth-username'] = state.username
  } catch (error) {
    console.error(error)
  }
}

export const refreshUserLogado = async (state, usuario) => {
  state.usuario = usuario
}

export const setlocalstorage = async (state, dados) => {
  try {
    state.cliente = null
    state.clientes = null
    state.accesscode = null
    state.logado = false
    state.token = dados.token
    state.expireat = dados.expire_at
    state.username = dados.username
    state.usuario = null

    if (dados.cliente) {
      state.cliente = dados.cliente
      sessionStorage.setItem('user_cliente', JSON.stringify(state.cliente))
    }
    if (dados.clientes) {
      state.clientes = dados.clientes
      sessionStorage.setItem('user_clientes', JSON.stringify(state.clientes))
    }
    if (dados.accesscode) {
      state.accesscode = dados.accesscode
      sessionStorage.setItem('user_accesscode', state.accesscode)
    }
    sessionStorage.setItem('user_token', state.token)
    sessionStorage.setItem('user_tokenexpire_at', state.expireat)
    sessionStorage.setItem('user_username', state.username)
  } catch (error) {
    console.error(error)
  }
}

export const getlocalstorage = (state) => {
  state.accesscode = sessionStorage.getItem('user_accesscode') ? sessionStorage.getItem('user_accesscode') : null
  state.token = sessionStorage.getItem('user_token') ? sessionStorage.getItem('user_token') : null
  state.expireat = sessionStorage.getItem('user_tokenexpire_at') ? sessionStorage.getItem('user_tokenexpire_at') : null
  state.username = sessionStorage.getItem('user_username') ? sessionStorage.getItem('user_username') : null
  state.clientes = sessionStorage.getItem('user_clientes') ? JSON.parse(sessionStorage.getItem('user_clientes')) : null
  state.cliente = sessionStorage.getItem('user_cliente') ? JSON.parse(sessionStorage.getItem('user_cliente')) : null
}

export const logout = (state) => {
  state.token = null
  state.accesscode = null
  state.expireat = null
  state.clientes = null
  state.cliente = null
  state.username = null
  state.usuario = null
  state.ret = null
  state.logado = false
  if (sessionStorage.getItem('user_cliente')) sessionStorage.removeItem('user_cliente')
  if (sessionStorage.getItem('user_clientes')) sessionStorage.removeItem('user_clientes')
  if (sessionStorage.getItem('user_accesscode')) sessionStorage.removeItem('user_accesscode')
  if (sessionStorage.getItem('user_username')) sessionStorage.removeItem('user_username')
  if (sessionStorage.getItem('user_token')) sessionStorage.removeItem('user_token')
  if (sessionStorage.getItem('user_tokenexpire_at')) sessionStorage.removeItem('user_tokenexpire_at')

  var instance = Vue.prototype.$axios
  instance.defaults.headers.common['x-auth-token'] = null
  instance.defaults.headers.common['x-auth-accesscode'] = null
  instance.defaults.headers.common['x-auth-username'] = null
  delete instance.defaults.headers.common['x-auth-token']
  delete instance.defaults.headers.common['x-auth-accesscode']
  delete instance.defaults.headers.common['x-auth-username']
}
