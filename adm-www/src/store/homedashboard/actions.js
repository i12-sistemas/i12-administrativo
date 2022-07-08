import Vue from 'vue'
import { ColetaSituacaoCliente } from 'src/mvc/models/enums/coletatypes'
import Usuario from 'src/mvc/models/usuario.js'

export async function togglemenu ({ commit, state, dispatch }) {
  Vue.prototype.$eventbus.$emit('togglemenu')
}

export async function init ({ commit, state, dispatch }) {
  var o = new ColetaSituacaoCliente('1')
  await commit('init', o.raw)
}

export async function refresh ({ commit, state }) {
  var usuario = new Usuario()
  var ret = await usuario.meuUsuario()
  if (!ret.ok) {
    await commit('seterror', ret.msg)
  } else {
    await commit('setuser', usuario)
    ret = await Vue.prototype.$axios.get('v1/painelcliente/dashboard/home').then(async response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          ret.data = data.data
          var novooption = state.options
          for (let index = 0; index < data.data.length; index++) {
            const element = data.data[index]
            for (let n = 0; n < novooption.length; n++) {
              if (novooption[n].value === element.id) {
                novooption[n].badge = element.qtde
                break
              }
            }
          }
          ret.data = novooption
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    if (ret.ok) {
      await commit('setoption', ret.data)
    } else {
      await commit('seterror', ret.msg)
    }
  }
  Vue.prototype.$eventbus.$emit('homedashboardupdated')
  return ret
}
