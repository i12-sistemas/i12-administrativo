import Vue from 'vue'
import Usuario from 'src/mvc/models/usuario.js'

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
    var lUser = new Usuario(dados.usuario)
    var lUserObj = JSON.parse(JSON.stringify(lUser))
    state.usuario = lUserObj
    console.log(state.usuario)
    state.logado = true
    var instance = Vue.prototype.$axios
    instance.defaults.headers.common['x-auth-token'] = state.token
    instance.defaults.headers.common['x-auth-username'] = state.usuario.login
  } catch (error) {
    console.error(error)
  }
}

export const setlocalstorage = async (state, dados) => {
  try {
    console.log('setlocalstorage', dados)
    localStorage.setItem('user_token', dados.token)
    localStorage.setItem('user_tokenexpire_at', dados.expire_at)
    localStorage.setItem('user_login', dados.login)

    state.logado = false
    state.token = dados.token
    state.expireat = dados.expire_at
    state.login = dados.login
    state.usuario = null
  } catch (error) {
    console.error(error)
  }
}

export const getlocalstorage = (state) => {
  // var clearall = false
  state.token = localStorage.getItem('user_token') ? localStorage.getItem('user_token') : null
  state.expireat = localStorage.getItem('user_tokenexpire_at') ? localStorage.getItem('user_tokenexpire_at') : null
  state.login = localStorage.getItem('user_login') ? localStorage.getItem('user_login') : null

  // if ((!state.token) || (!state.expireat)) clearall = true
  // if ((state.userlocal)) {
  //   if (!state.userlocal.hashid) clearall = true
  //   if (!state.userlocal.username) clearall = true
  // } else {
  //   clearall = true
  // }
  // if (clearall) {
  //   state.token = null
  //   state.userlocal = null
  //   state.expireat = null
  //   if (localStorage.getItem('usernametype')) localStorage.removeItem('usernametype')
  //   if (localStorage.getItem('user_token')) localStorage.removeItem('user_token')
  //   if (localStorage.getItem('user_tokenexpire_at')) localStorage.removeItem('user_tokenexpire_at')
  //   if (localStorage.getItem('user')) localStorage.removeItem('user')
  // }
}

export const logout = (state) => {
  state.token = null
  state.expireat = null
  state.login = null
  state.usuario = null
  state.ret = null
  state.logado = false
  if (localStorage.getItem('user_login')) localStorage.removeItem('user_login')
  if (localStorage.getItem('user_token')) localStorage.removeItem('user_token')
  if (localStorage.getItem('user_tokenexpire_at')) localStorage.removeItem('user_tokenexpire_at')

  var instance = Vue.prototype.$axios
  instance.defaults.headers.common['x-auth-token'] = null
  instance.defaults.headers.common['x-auth-username'] = null
  delete instance.defaults.headers.common['x-auth-token']
  delete instance.defaults.headers.common['x-auth-username']
}
