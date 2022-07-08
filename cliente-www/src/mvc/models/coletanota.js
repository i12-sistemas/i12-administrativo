// import moment from 'moment'
import Vue from 'vue'
import Motorista from 'src/mvc/models/motorista.js'
import Coleta from 'src/mvc/models/coleta.js'
import CargaEntrada from 'src/mvc/models/cargaentrada.js'
import CargaEntrega from 'src/mvc/models/cargaentrega.js'
import CargaTransfer from 'src/mvc/models/cargatransfer.js'
import Unidade from 'src/mvc/models/unidade.js'
import { EntregaStatus } from './enums/coletanotatypes'

class ColetaNota {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null
    this.uuid = ''
    this.localid = null
    this.dhlocal_data = null
    this.dhlocal_created_at = null
    this.coleta = null
    this.coletaavulsa = false
    this.coletaavulsaincluida = false
    this.coletaavulsaignorada = false
    this.idcoletaavulsa = null
    this.coletaavulsantentativa = 0
    this.idcoleta = null
    this.remetentecnpj = ''
    this.remetentenome = ''
    this.destinatariocnpj = ''
    this.destinatarionome = ''

    this.unidadeatual = null
    this.entregastatus = null
    this.cargaentradaitemid = null
    this.cargaentradaid = null

    this.motorista = new Motorista()
    this.notanumero = null
    this.notaserie = null
    this.notachave = ''
    this.created_at = null
    this.updated_at = null
    this.obs = ''
    this.docfiscal = null
    this.notadh = null
    this.notavalor = 0
    this.remetenteid = null
    this.destinatarioid = null
    this.coletaavulsaerror = false
    this.coletaavulsaerrormsg = ''
    this.endcoleta_cidadecodibge = null
    this.endcoleta_endereco = ''
    this.endcoleta_numero = ''
    this.endcoleta_bairro = ''
    this.endcoleta_cep = ''
    this.endcoleta_complemento = ''
    this.peso = 0
    this.qtde = 0
    this.especie = ''
    this.baixanfetentativas = 0
    this.baixanfemsg = ''
    this.baixanfestatus = null
    this.xmlprocessado = false
    this.guarita = false
    this.guaritaid = null
  }

  get status () {
    var ret = { ok: false, icon: null, msg: '', color: 'black', code: null }
    if (this.xmlprocessado) {
      ret.code = 1
      ret.icon = 'check_circle'
      ret.color = 'green'
      ret.ok = true
      ret.msg = 'Nota Ok'
      ret.msgshort = 'Ok'
    } else {
      if (this.baixanfestatus === 0) {
        ret.code = 2
        ret.icon = 'hourglass_empty'
        ret.color = 'blue'
        ret.msg = 'Pendente XML'
        ret.msgshort = 'Pendente'
      } else if (this.baixanfestatus === 1) {
        ret.ok = true
        ret.code = 3
        ret.color = 'orange'
        ret.icon = 'hourglass_bottom'
        ret.msg = 'Pendente processar XML'
        ret.msgshort = 'Pendente'
      } else if (this.baixanfestatus === 2) {
        ret.code = 4
        ret.icon = 'info'
        ret.color = 'red'
        ret.msg = 'Erro no download'
        ret.msgshort = 'Erro'
      } else if (this.baixanfestatus === 3) {
        ret.code = 5
        ret.icon = 'info'
        ret.color = 'blue-5'
        ret.msg = 'Ignorado'
        ret.msgshort = 'Ignorado'
      }
    }
    return ret
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (item.id) self.id = item.id
    if (item.uuid) self.uuid = item.uuid
    if (item.localid) self.localid = item.localid
    if (item.dhlocal_data) self.dhlocal_data = item.dhlocal_data
    if (item.dhlocal_created_at) self.dhlocal_created_at = item.dhlocal_created_at
    self.coletaavulsa = Vue.prototype.$helpers.toBool(item.coletaavulsa)
    self.coletaavulsaincluida = Vue.prototype.$helpers.toBool(item.coletaavulsaincluida)
    if (typeof item.coleta !== 'undefined') self.coleta = item.coleta
    if (typeof item.coletaavulsaignorada !== 'undefined') self.coletaavulsaignorada = Vue.prototype.$helpers.toBool(item.coletaavulsaignorada)
    if (typeof item.guarita !== 'undefined') self.guarita = Vue.prototype.$helpers.toBool(item.guarita)
    if (typeof item.guaritaid !== 'undefined') self.guaritaid = item.guaritaid

    if (typeof item.unidadeatual !== 'undefined') self.unidadeatual = new Unidade(item.unidadeatual)
    if (typeof item.entregastatus !== 'undefined') self.entregastatus = new EntregaStatus(item.entregastatus)
    if (typeof item.entregaupdated_at !== 'undefined') self.entregaupdated_at = item.entregaupdated_at
    if (typeof item.cargaentradaitemid !== 'undefined') self.cargaentradaitemid = item.cargaentradaitemid
    if (typeof item.cargaentradaid !== 'undefined') self.cargaentradaid = item.cargaentradaid

    if (item.idcoletaavulsa) self.idcoletaavulsa = item.idcoletaavulsa
    if (item.idcoleta) self.idcoleta = item.idcoleta
    if (typeof item.coletaavulsantentativa !== 'undefined') self.coletaavulsantentativa = item.coletaavulsantentativa
    if (item.remetentecnpj) self.remetentecnpj = item.remetentecnpj
    if (item.remetentenome) self.remetentenome = item.remetentenome
    if (item.destinatariocnpj) self.destinatariocnpj = item.destinatariocnpj
    if (item.destinatarionome) self.destinatarionome = item.destinatarionome
    if (item.motorista) self.motorista = new Motorista(item.motorista)
    if (item.notaserie) self.notaserie = item.notaserie
    if (item.notanumero) self.notanumero = item.notanumero
    if (item.notachave) self.notachave = item.notachave
    if (item.created_at) self.created_at = item.created_at
    if (item.updated_at) self.updated_at = item.updated_at
    if (item.obs) self.obs = item.obs
    if (item.docfiscal) self.docfiscal = item.docfiscal
    if (item.notadh) self.notadh = item.notadh
    if (item.notavalor) self.notavalor = item.notavalor
    if (item.remetenteid) self.remetenteid = item.remetenteid
    if (item.destinatarioid) self.destinatarioid = item.destinatarioid
    self.coletaavulsaerror = Vue.prototype.$helpers.toBool(item.coletaavulsaerror)
    if (item.coletaavulsaerrormsg) self.coletaavulsaerrormsg = item.coletaavulsaerrormsg
    if (item.endcoleta_cidadecodibge) self.endcoleta_cidadecodibge = item.endcoleta_cidadecodibge
    if (item.endcoleta_endereco) self.endcoleta_endereco = item.endcoleta_endereco
    if (item.endcoleta_numero) self.endcoleta_numero = item.endcoleta_numero
    if (item.endcoleta_bairro) self.endcoleta_bairro = item.endcoleta_bairro
    if (item.endcoleta_cep) self.endcoleta_cep = item.endcoleta_cep
    if (item.endcoleta_complemento) self.endcoleta_complemento = item.endcoleta_complemento
    if (item.peso) self.peso = item.peso
    if (item.qtde) self.qtde = item.qtde
    if (item.especie) self.especie = item.especie
    if (item.baixanfetentativas) self.baixanfetentativas = item.baixanfetentativas
    if (item.baixanfemsg) self.baixanfemsg = item.baixanfemsg
    self.baixanfestatus = item.baixanfestatus ? item.baixanfestatus : 0
    self.xmlprocessado = Vue.prototype.$helpers.toBool(item.xmlprocessado)
  }

  async find (pID) {
    var self = this
    self.limpardados()
    let ret = await Vue.prototype.$axios.get('v1/coletasnotas/' + pID).then(response => {
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

  async findrastreio (pChave) {
    let ret = await Vue.prototype.$axios.get('v1/painelcliente/rastreio/' + pChave).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          ret.data = data.data
          ret.ok = true
          ret.data = data.data
          if (ret.data.coleta) ret.data.coleta = new Coleta(ret.data.coleta)
          if (ret.data.nfe) ret.data.nfe = new ColetaNota(ret.data.nfe)
          if (ret.data.entrada) ret.data.entrada = new CargaEntrada(ret.data.entrada)
          if (ret.data.entrega) ret.data.entrega = new CargaEntrega(ret.data.entrega)
          if (ret.data.transferencias) {
            var lTransfers = []
            for (let index = 0; index < ret.data.transferencias.length; index++) {
              const t = ret.data.transferencias[index]
              var lItem = new CargaTransfer(t)
              if (lItem.id > 0) lTransfers.push(lItem)
            }
            ret.data.transferencias = (lTransfers ? lTransfers.length > 0 : false) ? lTransfers : null
          }
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }
}

export default ColetaNota
