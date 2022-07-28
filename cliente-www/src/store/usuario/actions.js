import Vue from 'vue'
import axios from 'axios'

export function checkip ({ commit, state, dispatch }) {
  if (state.ip) return
  var meuip = Vue.prototype.$helpers.getIp()
  meuip.then(function (retorno) {
    if (retorno.ok) {
      commit('setip', retorno.data)
    }
  })
}

export async function checklogon ({ commit, state, dispatch }) {
  var credentials = 'Basic ' + btoa(state.token + ':' + state.accesscode)
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
  var ret = await req.post('v1/contato/auth/checklogin').then(response => {
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
    await commit('setusuariologado', ret.data)
  }
}

export async function init ({ commit, state, dispatch }) {
  await commit('getlocalstorage')
  if (state.username && state.expireat && state.token && state.accesscode && !state.logado) {
    await dispatch('checklogon')
  }
}

export async function auth ({ commit, dispatch, state }, dados) {
  await commit('setlocalstorage', dados)
  if (state.token && state.accesscode) await dispatch('checklogon')
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

export async function refreshDashboard ({ commit, state }, showNotification = false) {
  Vue.prototype.$eventbus.$emit('usuariodash_loading')
  var contato = state.usuario
  var ret = await contato.refreshDashboard()
  if (ret.ok) {
    await commit('refreshUserLogado', contato)
    Vue.prototype.$eventbus.$emit('usuariodash_updated')
    if (showNotification) {
      Vue.prototype.$q.notify({
        color: 'positive',
        position: 'bottom',
        timeout: 1500,
        message: 'Informações atualizadas'
      })
    }
  } else {
    Vue.prototype.$eventbus.$emit('usuariodash_updated')
    if (showNotification) {
      Vue.prototype.$q.notify({
        color: 'red',
        position: 'bottom',
        timeout: 4000,
        message: 'Falha ao atualizar contadores',
        caption: ret.msg
      })
    }
  }
}
