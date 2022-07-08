import Motorista from 'src/mvc/models/motorista.js'
// import CargaEntradaItem from 'src/mvc/models/cargaentradaitem.js'
import Veiculo from 'src/mvc/models/veiculo.js'
import Usuario from 'src/mvc/models/usuario.js'
import Unidade from 'src/mvc/models/unidade.js'
import moment from 'moment'
import Vue from 'vue'
import { CargaEntradaTipo, CargaEntradaStatus } from './enums/cargastypes'

class CargaEntrada {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null
    this.tipo = new CargaEntradaTipo('1')
    this.status = new CargaEntradaStatus('1')
    this.unidadeentrada = new Unidade()
    this.motorista = new Motorista()
    this.veiculo = new Veiculo()
    this.dhentrada = moment().format('YYYY-MM-DD HH:mm:ss')
    this.created_at = null
    this.updated_at = null
    this.volqtde = 0
    this.peso = 0
    this.erroqtde = 0
    this.conferidoqtde = 0
    this.conferidoprogresso = 0
    this.erromsg = ''
    this.editadomanualmente = false
    this.created_usuario = new Usuario()
    this.updated_usuario = new Usuario()
    this.itens = null
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.id !== 'undefined') this.id = item.id
    if (typeof item.tipo !== 'undefined') this.tipo = new CargaEntradaTipo(item.tipo)
    if (typeof item.status !== 'undefined') this.status = new CargaEntradaStatus(item.status)
    if (typeof item.peso !== 'undefined') this.peso = item.peso
    if (typeof item.volqtde !== 'undefined') this.volqtde = item.volqtde
    if (typeof item.conferidoprogresso !== 'undefined') this.conferidoprogresso = Math.trunc(item.conferidoprogresso)
    if (typeof item.conferidoqtde !== 'undefined') this.conferidoqtde = item.conferidoqtde
    if (typeof item.dhentrada !== 'undefined') this.dhentrada = item.dhentrada
    if (typeof item.created_at !== 'undefined') this.created_at = item.created_at
    if (typeof item.updated_at !== 'undefined') this.updated_at = item.updated_at
    if (typeof item.erroqtde !== 'undefined') this.erroqtde = item.erroqtde
    if ((typeof item.erromsg !== 'undefined') && (this.erroqtde > 0)) {
      if (typeof item.erromsg === 'string') this.erromsg = JSON.parse(item.erromsg)
      if (typeof item.erromsg === 'object') this.erromsg = item.erromsg
    }
    if (typeof item.editadomanualmente !== 'undefined') this.editadomanualmente = Vue.prototype.$helpers.toBool(item.editadomanualmente)

    if (typeof item.veiculo !== 'undefined') self.veiculo = new Veiculo(item.veiculo)
    if (typeof item.motorista !== 'undefined') self.motorista = new Motorista(item.motorista)
    if (typeof item.unidadeentrada !== 'undefined') self.unidadeentrada = new Unidade(item.unidadeentrada)
    if (typeof item.created_usuario !== 'undefined') self.created_usuario = new Usuario(item.created_usuario)
    if (typeof item.updated_usuario !== 'undefined') self.updated_usuario = new Usuario(item.updated_usuario)

    // if (typeof item.itens !== 'undefined') {
    //   if (item.itens ? item.itens.length > 0 : false) {
    //     self.itens = []
    //     for (let index = 0; index < item.itens.length; index++) {
    //       var lItem = new CargaEntradaItem(item.itens[index])
    //       self.itens.push(lItem)
    //     }
    //   }
    // }
    // await self.abastecimentoTotaliza(false)
  }

  async find (pID) {
    var self = this
    self.limpardados()
    let ret = await Vue.prototype.$axios.get('v1/cargaentrada/carga/' + pID).then(response => {
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
}

export default CargaEntrada
