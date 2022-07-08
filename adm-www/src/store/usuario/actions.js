import Vue from 'vue'
import axios from 'axios'

export async function checklogon ({ commit, state, dispatch }) {
  var credentials = 'Basic ' + btoa(state.login + ':' + state.token)
  var req = axios.create({
    baseURL: Vue.prototype.$configini.API_URL,
    withCredentials: false,
    headers: {
      'Authorization': credentials,
      'Access-Control-Allow-Origin': '*',
      'Access-Control-Allow-Headers': 'Authorization',
      'Access-Control-Allow-Methods': 'GET, POST, OPTIONS, PUT, PATCH, DELETE',
      'Content-Type': 'application/json;charset=UTF-8'
    }
  })
  var ret = await req.post('v1/auth/checklogin').then(response => {
    let data = response.data
    return data
  }
  ).catch(error => {
    let msg = error
    if (error.response) {
      msg = 'Code: ' + error.response.status + ' - ' + error.response.data.message
    } else {
      msg = error.message
    }
    return { ok: false, msg: 'Falha ao consultar servidor online: ' + msg }
  })
  await commit('setret', ret)
  if (ret.ok) {
    console.log(ret.data)
    await commit('setusuariologado', ret.data)
    // if (state.logado) {
    //   var pusher = Vue.prototype.$pusher
    //   var channelDeviceAll = pusher.subscribe('allusersconnected')
    //   var channelDeviceUser = pusher.subscribe('usr-' + state.user.username.toString().toLowerCase())
    //   channelDeviceUser.bind('disconnect', function (params) {
    //     var b = Vue.prototype.$helpers.toBool(params)
    //     if (b) dispatch('logout')
    //   })
    //   channelDeviceAll.bind('disconnect', function (params) {
    //     var b = Vue.prototype.$helpers.toBool(params)
    //     if (b) dispatch('logout')
    //   })

    //   channelDeviceUser.bind('info', function (params) {
    //     if (typeof params === 'object') {
    //       Vue.prototype.$eventbus.$emit('notification_add', params)
    //     }
    //   })
    //   channelDeviceAll.bind('info', function (params) {
    //     if (typeof params === 'object') Vue.prototype.$eventbus.$emit('notification_add', params)
    //   })
    // } else {
    //   Vue.prototype.$pusher.unsubscribe('allusersconnected')
    //   Vue.prototype.$pusher.unsubscribe('usr-' + state.user.username.toString().toLowerCase())
    // }
  }
}

export async function init ({ commit, state, dispatch }) {
  await commit('getlocalstorage')
  if (state.login && state.expireat && state.token && !state.logado) {
    await dispatch('checklogon')
  }
}

export async function auth ({ commit, dispatch }, dados) {
  await commit('setlocalstorage', dados)
  await dispatch('checklogon')
}

export async function adderror401 ({ commit, state, dispatch }) {
  await commit('adderror401')
  if (state.axioserror401count >= 4) {
    await dispatch('logout')
    Vue.prototype.$q.notify({
      message: 'Sua sessão foi encerrada!',
      color: 'red',
      onDismiss: () => {
      },
      actions: [{
        icon: 'close',
        color: 'white'
      }]
    })
  }
}

export async function reseterror401 ({ commit }) {
  await commit('reseterror401')
}

export async function logout ({ commit }) {
  commit('logout')
  this.$router.push({ name: 'login.usuario' })
  return true
}
