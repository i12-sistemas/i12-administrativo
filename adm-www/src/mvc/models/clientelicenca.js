import Vue from 'vue'
import Cliente from 'src/mvc/models/cliente.js'
// import DialogClienteAdd from 'src/pages/cadastro/clienteusuarios/clienteadddialog.vue'
// import DialogUsuarioAdd from 'src/pages/cadastro/clienteusuarios/clienteusuarioadddialog.vue'
// import DialogUsuarioEdit from 'src/pages/cadastro/clienteusuarios/clienteusuarioeditdialog.vue'

class ClienteLicenca {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.cliente = null
    this.numero = null
    this.datavencimento = null
    this.expirado = true
    this.databloqueio = null
    this.bloqueado = true
    this.status = null
    this.diasrestantes = 0
    this.dataativacao = null
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.cliente !== 'undefined') self.cliente = new Cliente(item.cliente)

    if (typeof item.licencaatual !== 'undefined') {
      item = item.licencaatual
      if (typeof item.numero !== 'undefined') self.numero = item.numero
      if (typeof item.dataativacao !== 'undefined') self.dataativacao = item.dataativacao
      if (typeof item.datavencimento !== 'undefined') self.datavencimento = item.datavencimento
      if (typeof item.expirado !== 'undefined') self.expirado = Vue.prototype.$helpers.toBool(item.expirado)
      if (typeof item.databloqueio !== 'undefined') self.databloqueio = item.databloqueio
      if (typeof item.bloqueado !== 'undefined') self.bloqueado = Vue.prototype.$helpers.toBool(item.bloqueado)
      if (typeof item.status !== 'undefined') self.status = item.status
      if (typeof item.diasrestantes !== 'undefined') self.diasrestantes = item.diasrestantes
    }
  }

  async gerarlicenca (pDoc, pModo, pData, pObs, pForce) {
    try {
      // if (novousuario) {
      //   if (!Vue.prototype.$helpers.validaEmail(self.email)) throw new Error('E-mail inválido')
      //   if (self.nome ? self.nome === '' : true) throw new Error('Nome inválido')
      //   if (typeof self.clienteid === 'undefined') throw new Error('Necessário informar um cliente para criar um usuário')
      //   if (!(self.clienteid > 0)) throw new Error('Necessário informar um cliente para criar um usuário')
      // }
    } catch (error) {
      return { ok: false, msg: error.message, warning: true }
    }
    var params = {
      modo: pModo,
      cnpj: pDoc,
      data: pData,
      obs: pObs
    }
    if (pForce) params['force'] = 1
    let ret = await Vue.prototype.$axios.post('v1/admin/clientes/licencas/gerar', params).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        ret.ok = data.ok
        ret.id = data.id
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }
}

export default ClienteLicenca
