// import AddDialog from 'src/pages/operacional/cargas/entrega/mdfe/add-dialog.vue'
import Usuario from 'src/mvc/models/usuario.js'
import Motorista from 'src/mvc/models/motorista.js'

import Vue from 'vue'
import { CargaEntregaBaixaOperacao, CargaEntregaBaixaOrigem } from './enums/cargastypes'

class CargaEntregaBaixa {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.baixadhlocal = null
    this.created_at = null
    this.imgmd5 = null
    this.imgsize = 0
    delete this.motorista
    delete this.created_usuario
    this.imgsize = 0
    delete this.operacao
    delete this.origem
    this.uuid = null
    this.recusadoqtde = 0
    delete this.recusadojson
    delete this.comprovanteurl
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.baixadhlocal !== 'undefined') self.baixadhlocal = item.baixadhlocal
    if (typeof item.created_at !== 'undefined') self.created_at = item.created_at
    if (typeof item.recusadoqtde !== 'undefined') self.recusadoqtde = item.recusadoqtde
    if (typeof item.imgmd5 !== 'undefined') self.imgmd5 = item.imgmd5
    if (typeof item.imgsize !== 'undefined') self.imgsize = item.imgsize
    if (typeof item.operacao !== 'undefined') self.operacao = new CargaEntregaBaixaOperacao(item.operacao)
    if (typeof item.origem !== 'undefined') self.origem = new CargaEntregaBaixaOrigem(item.origem)
    if (typeof item.uuid !== 'undefined') self.uuid = item.uuid
    if (typeof item.created_usuario !== 'undefined') self.created_usuario = new Usuario(item.created_usuario)
    if (typeof item.motorista !== 'undefined') self.motorista = new Motorista(item.motorista)
  }

  get imagemdisponivel () {
    return (this.imgmd5 ? this.imgmd5 !== '' : false) && (this.imgsize ? this.imgsize > 0 : false)
  }

  async downloadcomprovante (app, download = true) {
    var self = this
    var ret = { ok: false }
    var dialog = Vue.prototype.$helpers.dialogProgress(app, 'Comprovante da baixa', 'Consultando arquivo')
    try {
      ret = await self.GetComprovanteUrl()
      if (!ret.ok) throw new Error(ret.msg)
      if (download) Vue.prototype.$helpers.showPrint(ret.msg)
    } catch (error) {
      return { ok: false, msg: error.message, warning: true }
    } finally {
      dialog.hide()
    }
    return ret
  }

  async GetComprovanteUrl () {
    var self = this
    try {
      if (self.imgmd5 ? self.imgmd5 === '' : false) throw new Error('Nenhum imagem disponível!')
    } catch (error) {
      return { ok: false, msg: error.message, warning: true }
    }
    let ret = await Vue.prototype.$axios.get('v1/cargaentrega/comprovante/' + self.imgmd5).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.ok = data.ok
        ret.msg = data.msg ? data.msg : ''
        self.comprovanteurl = data.msg
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  // async getPrintUrl () {
  //   var self = this
  //   let ret = await Vue.prototype.$axios.get('v1/cargaentrega/carga/' + self.id + '/print/detalhe').then(response => {
  //     let data = response.data
  //     var ret = { ok: false, msg: '' }
  //     if (data) {
  //       ret.msg = data.msg ? data.msg : ''
  //       ret.ok = data.ok ? data.ok : false
  //     }
  //     return ret
  //   }).catch(error => {
  //     return Vue.prototype.$helpers.errorReturn(error)
  //   })
  //   return ret
  // }
}

export default CargaEntregaBaixa
