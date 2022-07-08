import Motorista from 'src/mvc/models/motorista.js'
import Veiculo from 'src/mvc/models/veiculo.js'
import Usuario from 'src/mvc/models/usuario.js'
import Unidade from 'src/mvc/models/unidade.js'
import moment from 'moment'
import { CargaTransferStatus } from './enums/cargastypes'

class CargaTransfer {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null
    this.status = new CargaTransferStatus('1')
    this.unidadeentrada = new Unidade()
    this.unidadesaida = new Unidade()
    this.motorista = new Motorista()
    this.veiculo = new Veiculo()
    this.saidadh = null
    this.entradadh = null
    this.created_at = moment().format('YYYY-MM-DD HH:mm:ss')
    this.updated_at = moment().format('YYYY-MM-DD HH:mm:ss')
    this.numerogr = ''
    this.volqtde = 0
    this.peso = 0
    this.erroqtde = 0
    this.erromsg = ''
    this.conferidoentradaqtde = 0
    this.conferidoentradaprogresso = 0
    this.entrada_usuario = null
    this.saida_usuario = null
    this.created_usuario = null
    this.updated_usuario = null
    this.itens = null
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.id !== 'undefined') self.id = item.id
    if (typeof item.status !== 'undefined') self.status = new CargaTransferStatus(item.status)
    if (typeof item.veiculo !== 'undefined') self.veiculo = new Veiculo(item.veiculo)
    if (typeof item.motorista !== 'undefined') self.motorista = new Motorista(item.motorista)
    if (typeof item.unidadeentrada !== 'undefined') self.unidadeentrada = new Unidade(item.unidadeentrada)
    if (typeof item.unidadesaida !== 'undefined') self.unidadesaida = new Unidade(item.unidadesaida)
    if (typeof item.numerogr !== 'undefined') self.numerogr = item.numerogr
    if (typeof item.peso !== 'undefined') self.peso = item.peso
    if (typeof item.volqtde !== 'undefined') self.volqtde = item.volqtde
    if (typeof item.entradadh !== 'undefined') self.entradadh = item.entradadh
    if (typeof item.conferidoentradaprogresso !== 'undefined') this.conferidoentradaprogresso = Math.trunc(item.conferidoentradaprogresso)
    if (typeof item.conferidoentradaqtde !== 'undefined') this.conferidoentradaqtde = item.conferidoentradaqtde
    if (typeof item.saidadh !== 'undefined') self.saidadh = item.saidadh
    if (typeof item.created_at !== 'undefined') self.created_at = item.created_at
    if (typeof item.updated_at !== 'undefined') self.updated_at = item.updated_at
    if (typeof item.erroqtde !== 'undefined') self.erroqtde = item.erroqtde
    // if ((typeof item.erromsg !== 'undefined') && (self.erroqtde > 0)) {
    //   if (typeof item.erromsg === 'object') self.erromsg = item.erromsg
    //   if (typeof item.erromsg === 'string') {
    //     self.erromsg = JSON.parse(item.erromsg)
    //     for (let index = 0; index < self.erromsg.length; index++) {
    //       var eti = new Etiqueta(self.erromsg[index].etiqueta)
    //       self.erromsg[index].etiqueta = eti
    //     }
    //   }
    // }
    if (typeof item.entrada_usuario !== 'undefined') self.entrada_usuario = new Usuario(item.entrada_usuario)
    if (typeof item.saida_usuario !== 'undefined') self.saida_usuario = new Usuario(item.saida_usuario)
    if (typeof item.created_usuario !== 'undefined') self.created_usuario = new Usuario(item.created_usuario)
    if (typeof item.updated_usuario !== 'undefined') self.updated_usuario = new Usuario(item.updated_usuario)

    // if (typeof item.itens !== 'undefined') {
    //   if (item.itens ? item.itens.length > 0 : false) {
    //     self.itens = []
    //     for (let index = 0; index < item.itens.length; index++) {
    //       var lItem = new CargaTransferItem(item.itens[index])
    //       self.itens.push(lItem)
    //     }
    //   }
    // }
  }
  // async find (pID) {
  //   var self = this
  //   self.limpardados()
  //   let ret = await Vue.prototype.$axios.get('v1/cargatransfer/carga/' + pID).then(response => {
  //     let data = response.data
  //     var ret = { ok: false, msg: '' }
  //     if (data) {
  //       ret.msg = data.msg ? data.msg : ''
  //       if (data.ok) {
  //         ret.ok = true
  //         self.cloneFrom(data.data)
  //       }
  //     }
  //     return ret
  //   }).catch(error => {
  //     return Vue.prototype.$helpers.errorReturn(error)
  //   })
  //   return ret
  // }
}

export default CargaTransfer
