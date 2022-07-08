import Vue from 'vue'
// import Unidade from 'src/mvc/models/unidade.js'
class Usuario {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.username = null
    this.usernametype = null
    this.nome = ''
    this.nome_old = ''
    this.login = ''
    this.ativo = true
    this.pemitefup = false
    this.permitecoletasver = false
    this.permitestatusgeral = false
    this.clienteusuariodadmin = false
    this.email = ''
    this.celular = ''
    this.celular_old = ''
    this.fotourl = null
    this.clientes = null
    this.clientescount = 0
    this.clientescnpj = []

    this.followupclientes = null
    this.followupcount = 0
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.usernametype !== 'undefined') self.usernametype = item.usernametype
    if (typeof item.username !== 'undefined') self.username = item.username
    if (typeof item.nome !== 'undefined') self.nome = item.nome.toString().toUpperCase()
    self.nome_old = item.nome.toString().toUpperCase()
    if (typeof item.email !== 'undefined') self.email = item.email.toString().toLowerCase()
    if (typeof item.celular !== 'undefined') self.celular = item.celular
    if (typeof item.celular !== 'undefined') self.celular = item.celular
    self.celular_old = self.celular
    if (typeof item.fotourl !== 'undefined') self.fotourl = item.fotourl

    self.ativo = Vue.prototype.$helpers.toBool(item.ativo)
    self.pemitefup = Vue.prototype.$helpers.toBool(item.pemitefup)
    self.permitecoletasver = Vue.prototype.$helpers.toBool(item.permitecoletasver)
    self.permitestatusgeral = Vue.prototype.$helpers.toBool(item.permitestatusgeral)
    self.clienteusuariodadmin = Vue.prototype.$helpers.toBool(item.clienteusuariodadmin)

    if (typeof item.clientes !== 'undefined') {
      if (item.clientes ? item.clientes.length > 0 : false) {
        self.clientes = []
        self.followupclientes = []
        for (let index = 0; index < item.clientes.length; index++) {
          const c = item.clientes[index]
          self.clientes.push(c)
          self.clientescnpj.push(c.cnpj)
          if ((c.followupid ? c.followupid !== '' : false) && (c.fantasia_followup ? c.fantasia_followup !== '' : false)) {
            self.followupclientes.push(c)
          }
        }
        self.clientescount = self.clientes ? self.clientes.length : 0
        self.followupcount = self.followupclientes ? self.followupclientes.length : 0
        if (self.followupcount === 0) self.followupclientes = null
      }
    }
  }

  async meuUsuario () {
    var self = this
    self.limpardados()
    let ret = await Vue.prototype.$axios.get('v1/painelcliente/meuperfil').then(async response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          await self.cloneFrom(data.data)
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async saveMeuUsuario () {
    var self = this
    try {
      if (!self.nome) throw new Error('Nome é obrigatório')
      if (self.nome === '') throw new Error('Nome é obrigatório')
    } catch (error) {
      return { ok: false, msg: error.message, warning: true }
    }
    let params = {
      nome: self.nome,
      celular: self.celular
    }
    let ret = await Vue.prototype.$axios.post('v1/painelcliente/meuperfil', params).then(async response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          if (data.data) await self.cloneFrom(data.data)
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async permite (pIDPermissao) {
    try {
      var self = this
      if (!self.permissoes) throw new Error('Sem permissao')
      if (self.permissoes.length === 0) throw new Error('Sem permissao')

      var idx = await self.permissoes.findIndex((element) => {
        return (element.idpermissao === pIDPermissao)
      })
      return (idx ? idx >= 0 : false)
    } catch (error) {
      return false
    }
  }
}

export default Usuario
