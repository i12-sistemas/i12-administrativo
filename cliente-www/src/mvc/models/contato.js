import Vue from 'vue'

class Contato {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null
    this.whatsapp = null
    this.email = null
    delete this.fotoiconurl
    this.nome = ''
    delete this.permissoes
    delete this.nome_old
    delete this.dashboard_atendimento
  }

  get ehadministrador () {
    var b = false
    if (this.permissoes ? this.permissoes.length > 0 : false) {
      b = this.permissoes.indexOf('administrador') >= 0
    }
    return b
  }

  get ehoperador () {
    var b = false
    if (this.permissoes ? this.permissoes.length > 0 : false) {
      b = (this.permissoes.indexOf('operador') >= 0) || (this.permissoes.indexOf('administrador') >= 0)
    }
    return b
  }

  get ehfinanceiro () {
    var b = false
    if (this.permissoes ? this.permissoes.length > 0 : false) {
      b = (this.permissoes.indexOf('financeiro') >= 0) || (this.permissoes.indexOf('administrador') >= 0)
    }
    return b
  }

  get ehcontador () {
    var b = false
    if (this.permissoes ? this.permissoes.length > 0 : false) {
      b = (this.permissoes.indexOf('contador') >= 0) || (this.permissoes.indexOf('administrador') >= 0)
    }
    return b
  }

  async permite (permissao) {
    var b = false
    if (this.permissoes ? this.permissoes.length > 0 : false) {
      b = this.permissoes.indexOf(permissao) >= 0
    }
    return b
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.id !== 'undefined') self.id = item.id
    if (typeof item.whatsapp !== 'undefined') self.whatsapp = item.whatsapp
    if (typeof item.email !== 'undefined') self.email = item.email.toString().toLowerCase()
    if (typeof item.nome !== 'undefined') self.nome = item.nome.toString().toUpperCase()
    if (typeof item.permissoes !== 'undefined') self.permissoes = item.permissoes
    self.nome_old = item.nome.toString().toUpperCase()
    if (typeof item.fotoiconurl !== 'undefined') self.fotoiconurl = item.fotoiconurl
  }

  async refreshDashboard () {
    var self = this
    try {
      if (!self.ehoperador) throw new Error('Acesso negado. [nível operador]')
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }
    let ret = await Vue.prototype.$axios.get('v1/painelcliente/atendimentos/contadores').then(async response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        ret.ok = data.ok
        if (ret.ok) self.dashboard_atendimento = data.data
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }
}

export default Contato
