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
    state.usernametype = dados.usernametype
    state.accesscode = dados.accesscode
    state.token = dados.usertoken
    state.expireat = dados.usertokenexpire_at
    var lUser = new Usuario(dados.user)
    var lUserObj = JSON.parse(JSON.stringify(lUser))
    state.user = lUserObj
    state.logado = true
    localStorage.setItem('usernametype', dados.usernametype)
    localStorage.setItem('user_token', dados.usertoken)
    localStorage.setItem('user_tokenexpire_at', dados.usertokenexpire_at)

    var instance = Vue.prototype.$axios
    instance.defaults.headers.common['x-auth-token'] = state.token
    instance.defaults.headers.common['x-auth-accesscode'] = state.accesscode
    instance.defaults.headers.common['x-auth-uuid'] = state.uuid
    instance.defaults.headers.common['x-auth-username'] = state.user.username
    instance.defaults.headers.common['x-auth-usernametype'] = state.usernametype
  } catch (error) {
    console.error(error)
  }
}

export const setlocalstorage = async (state, dados) => {
  try {
    localStorage.setItem('user_token', dados.usertoken)
    localStorage.setItem('user_tokenexpire_at', dados.usertokenexpire_at)
    localStorage.setItem('user', JSON.stringify(dados.user))
    state.usernametype = dados.usernametype
    state.token = dados.usertoken
    state.expireat = dados.usertokenexpire_at
    state.userlocal = dados.user
  } catch (error) {
    console.error(error)
  }
}

export const getlocalstorage = (state) => {
  var clearall = false
  state.token = localStorage.getItem('user_token') ? localStorage.getItem('user_token') : null
  state.expireat = localStorage.getItem('user_tokenexpire_at') ? localStorage.getItem('user_tokenexpire_at') : null
  state.userlocal = localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null
  state.uuid = localStorage.getItem('uuid') ? localStorage.getItem('uuid') : null
  if (state.uuid === '') state.uuid = null
  if (!state.uuid) {
    state.uuid = Vue.prototype.$helpers.generateUUID()
    localStorage.setItem('uuid', state.uuid)
  }
  if ((!state.token) || (!state.expireat)) clearall = true
  if ((state.userlocal)) {
    if (!state.userlocal.hashid) clearall = true
    if (!state.userlocal.username) clearall = true
  } else {
    clearall = true
  }
  if (clearall) {
    state.token = null
    state.userlocal = null
    state.expireat = null
    if (localStorage.getItem('usernametype')) localStorage.removeItem('usernametype')
    if (localStorage.getItem('user_token')) localStorage.removeItem('user_token')
    if (localStorage.getItem('user_tokenexpire_at')) localStorage.removeItem('user_tokenexpire_at')
    if (localStorage.getItem('user')) localStorage.removeItem('user')
  }
}

export const logout = (state) => {
  state.usernametype = null
  state.accesscode = null
  state.token = null
  state.expireat = null
  state.userlocal = null
  state.user = null
  state.ret = null
  state.logado = false
  if (localStorage.getItem('usernametype')) localStorage.removeItem('usernametype')
  if (localStorage.getItem('user_token')) localStorage.removeItem('user_token')
  if (localStorage.getItem('user_tokenexpire_at')) localStorage.removeItem('user_tokenexpire_at')
  if (localStorage.getItem('user')) localStorage.removeItem('user')
  var instance = Vue.prototype.$axios
  instance.defaults.headers.common['x-auth-token'] = null
  instance.defaults.headers.common['x-auth-accesscode'] = null
  instance.defaults.headers.common['x-auth-uuid'] = null
  instance.defaults.headers.common['x-auth-username'] = null
  instance.defaults.headers.common['x-auth-usernametype'] = null

  delete instance.defaults.headers.common['x-auth-token']
  delete instance.defaults.headers.common['x-auth-accesscode']
  delete instance.defaults.headers.common['x-auth-uuid']
  delete instance.defaults.headers.common['x-auth-username']
  delete instance.defaults.headers.common['x-auth-usernametype']
}
