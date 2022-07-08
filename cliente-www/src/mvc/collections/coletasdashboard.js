import Vue from 'vue'
import moment from 'moment'

class ColetasDashboard {
  constructor () {
    this.limpardados()
    this.updated = null
  }

  async limpardados () {
    this.emaberto = {
      total: 0,
      semmotorista: 0,
      revisaoorcamento: 0,
      naoliberado: 0,
      cargaurgente: 0,
      futuro: 0,
      hoje: 0,
      ontem: 0,
      atrasado: 0
    }

    this.encerrados = {
      hoje: 0,
      semana: 0,
      mes: 0
    }
    this.cancelados = {
      hoje: 0,
      semana: 0,
      mes: 0
    }
  }

  async cloneFrom (pItem) {
    var self = this
    self.limpardados()
    if (!pItem) return
    var item = pItem.emaberto
    if (item.total) self.emaberto.total = parseFloat(item.total)
    if (item.semmotorista) self.emaberto.semmotorista = parseFloat(item.semmotorista)
    if (item.cargaurgente) self.emaberto.cargaurgente = parseFloat(item.cargaurgente)
    if (item.naoliberado) self.emaberto.naoliberado = parseFloat(item.naoliberado)
    if (item.revisaoorcamento) self.emaberto.revisaoorcamento = parseFloat(item.revisaoorcamento)
    if (item.futuro) self.emaberto.futuro = parseFloat(item.futuro)
    if (item.hoje) self.emaberto.hoje = parseFloat(item.hoje)
    if (item.ontem) self.emaberto.ontem = parseFloat(item.ontem)
    if (item.atrasado) self.emaberto.atrasado = parseFloat(item.atrasado)

    item = pItem.encerrados
    if (item.hoje) self.encerrados.hoje = parseFloat(item.hoje)
    if (item.semana) self.encerrados.semana = parseFloat(item.semana)
    if (item.mes) self.encerrados.mes = parseFloat(item.mes)

    item = pItem.cancelados
    if (item.hoje) self.cancelados.hoje = parseFloat(item.hoje)
    if (item.semana) self.cancelados.semana = parseFloat(item.semana)
    if (item.mes) self.cancelados.mes = parseFloat(item.mes)
  }

  async fetch () {
    var self = this
    self.limpardados()
    let params = {
      showall: self.showall ? 1 : 0
    }
    // if (self.numero !== null) params['numero'] = self.numero
    // if (self.produtosperigosos !== null) params['produtosperigosos'] = self.produtosperigosos ? 1 : 0
    // if (self.veiculoexclusico !== null) params['veiculoexclusico'] = self.veiculoexclusico ? 1 : 0
    // if (self.cargaurgente !== null) params['cargaurgente'] = self.cargaurgente ? 1 : 0
    // if (self.situacao !== null) params['situacao'] = self.situacao.join(',')

    let ret = await Vue.prototype.$axios.get('v1/coletas/dashboard1', { params: params }).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        ret.ok = data.ok
        if (data.ok) {
          data = data.data
          self.updated = moment()
          self.cloneFrom(data)
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }
}
export default ColetasDashboard
