import Vue from 'vue'
// import Unidade from 'src/mvc/models/unidade.js'
class Usuario {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.codusuario = null
    this.login = null
    this.email = null
    delete this.fotoiconurl
    this.nome = ''
    this.nome_old = ''
    this.ativo = true
    delete this.colaborador
    delete this.contato
    delete this.permissoes
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.codusuario !== 'undefined') self.codusuario = item.codusuario
    if (typeof item.login !== 'undefined') self.login = item.login
    if (typeof item.email !== 'undefined') self.email = item.email.toString().toLowerCase()
    if (typeof item.nome !== 'undefined') self.nome = item.nome.toString().toUpperCase()
    self.nome_old = item.nome.toString().toUpperCase()
    if (typeof item.fotoiconurl !== 'undefined') self.fotoiconurl = item.fotoiconurl
    if (typeof item.colaborador !== 'undefined') self.colaborador = item.colaborador
    if (typeof item.contato !== 'undefined') self.contato = item.contato
    if (typeof item.permissoes !== 'undefined') self.permissoes = item.permissoes
    self.ativo = Vue.prototype.$helpers.toBool(item.ativo)
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
