import Vue from 'vue'

class Atendimento {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null
    this.codigoacesso = null
    this.situacao = null
    this.encerrado = false
    this.prioridade = 1
    this.pendentecliente = false
    this.problemareclamado = null
    this.dtabertura = null
    this.categoria = null
    this.classificacao = null
    this.fase = null
    this.ultimainteracao = null
    this.contato_leitura_at = null
    delete this.prazoprevisao
    delete this.dtfechamento
    delete this.contato
    delete this.cliente
    delete this.interacoes
  }

  get lido () { return this.contato_leitura_at }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.id !== 'undefined') this.id = item.id
    if (typeof item.codigoacesso !== 'undefined') this.codigoacesso = item.codigoacesso
    if (typeof item.prioridade !== 'undefined') this.prioridade = item.prioridade
    if (typeof item.situacao !== 'undefined') this.situacao = item.situacao
    this.encerrado = this.situacao ? (this.situacao === 'Encerrada') : false
    if (typeof item.problemareclamado !== 'undefined') this.problemareclamado = item.problemareclamado
    self.pendentecliente = Vue.prototype.$helpers.toBool(item.pendentecliente)
    if (typeof item.dtabertura !== 'undefined') this.dtabertura = item.dtabertura
    if (typeof item.dtfechamento !== 'undefined') this.dtfechamento = item.dtfechamento
    if (typeof item.prazoprevisao !== 'undefined') this.prazoprevisao = item.prazoprevisao
    if (typeof item.categoria !== 'undefined') this.categoria = item.categoria
    if (typeof item.classificacao !== 'undefined') this.classificacao = item.classificacao
    if (typeof item.fase !== 'undefined') this.fase = item.fase
    if (typeof item.ultimainteracao !== 'undefined') this.ultimainteracao = item.ultimainteracao
    if (typeof item.contato_leitura_at !== 'undefined') this.contato_leitura_at = item.contato_leitura_at
    if (typeof item.contato !== 'undefined') this.contato = item.contato
    if (typeof item.cliente !== 'undefined') this.cliente = item.cliente
    if (typeof item.interacoes !== 'undefined') this.interacoes = item.interacoes
  }

  async find (pNumero) {
    var self = this
    self.limpardados()
    let ret = await Vue.prototype.$axios.get('v1/painelcliente/atendimentos/atendimento/' + pNumero).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          self.cloneFrom(data.data)
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async saveAdd () {
    var self = this
    try {
      if (!self.problemareclamado) throw new Error('Nenhum descrição na solicitação de atendimento')
      if (self.problemareclamado ? self.problemareclamado.length < 10 : true) throw new Error('Descrição na solicitação deve ter no mínimo 10 caracteres')
      if (typeof self.prioridade === 'undefined') throw new Error('Prioridade inválida')
      if (self.prioridade < 0) throw new Error('Prioridade inválida')
      if (self.prioridade > 3) throw new Error('Prioridade inválida')
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }
    var params = {
      mensagem: self.problemareclamado,
      prioridade: self.prioridade
    }
    let ret = await Vue.prototype.$axios.post('v1/painelcliente/atendimentos/atendimento/add', params).then(response => {
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

  async sendInteracao (pMsg) {
    var self = this
    var params = {
      mensagem: pMsg
    }
    let ret = await Vue.prototype.$axios.post('v1/painelcliente/atendimentos/atendimento/' + self.id + '/interacao/add', params).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        ret.ok = data.ok
        ret.data = data.data
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async sendLido (pLido = true) {
    var self = this
    var params = {
      lido: pLido ? 'lido' : 'naoligo'
    }
    let ret = await Vue.prototype.$axios.post('v1/painelcliente/atendimentos/atendimento/' + self.id + '/lido', params).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        ret.ok = data.ok
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }
}

export default Atendimento
