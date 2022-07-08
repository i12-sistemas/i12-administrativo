import PaleteItem from 'src/mvc/models/paleteitem.js'
import Etiqueta from 'src/mvc/models/etiqueta.js'
import Usuario from 'src/mvc/models/usuario.js'
import Unidade from 'src/mvc/models/unidade.js'
import moment from 'moment'
import { PaletesStatus } from './enums/cargastypes'
import Axios from 'axios'
import Vue from 'vue'

class Palete {
  constructor (pItem) {
    this.limpardados()
    this.dataraw = null
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null
    this.status = new PaletesStatus('1')
    this.unidade = new Unidade()
    this.created_at = moment().format('YYYY-MM-DD HH:mm:ss')
    this.updated_at = moment().format('YYYY-MM-DD HH:mm:ss')
    this.volqtde = 0
    this.pesototal = 0
    this.erroqtde = 0
    this.erromsg = ''
    this.created_usuario = null
    this.updated_usuario = null
    this.ean13 = null
    this.itens = null
    this.descricao = null
  }

  async cloneFrom (pItem) {
    var self = this
    self.limpardados()
    var item = pItem
    if (!item) return
    if (typeof item === 'object') {
      if (item.constructor.name === 'Palete') {
        if (item.id ? !(item.id > 0) : true) return
        item = pItem.dataraw
      }
    }
    self.dataraw = item
    if (typeof item.id !== 'undefined') self.id = item.id
    if (typeof item.status !== 'undefined') self.status.value = item.status
    if (typeof item.ean13 !== 'undefined') self.ean13 = item.ean13
    if (typeof item.descricao !== 'undefined') self.descricao = item.descricao
    if (typeof item.unidade !== 'undefined') self.unidade = new Unidade(item.unidade)
    if (typeof item.created_at !== 'undefined') self.created_at = item.created_at
    if (typeof item.updated_at !== 'undefined') self.updated_at = item.updated_at
    if (typeof item.pesototal !== 'undefined') self.pesototal = item.pesototal
    if (typeof item.volqtde !== 'undefined') self.volqtde = item.volqtde
    if (typeof item.erroqtde !== 'undefined') self.erroqtde = item.erroqtde
    if ((typeof item.erromsg !== 'undefined') && (self.erroqtde > 0)) {
      if (typeof item.erromsg === 'object') self.erromsg = item.erromsg
      if (typeof item.erromsg === 'string') {
        self.erromsg = JSON.parse(item.erromsg)
        for (let index = 0; index < self.erromsg.length; index++) {
          var eti = self.erromsg[index].etiqueta
          if (eti) {
            eti = new Etiqueta(self.erromsg[index].etiqueta)
            self.erromsg[index].etiqueta = eti
          }
        }
      }
    }
    if (typeof item.created_usuario !== 'undefined') self.created_usuario = new Usuario(item.created_usuario)
    if (typeof item.updated_usuario !== 'undefined') self.updated_usuario = new Usuario(item.updated_usuario)

    if (typeof item.itens !== 'undefined') {
      if (item.itens ? item.itens.length > 0 : false) {
        self.itens = []
        for (let index = 0; index < item.itens.length; index++) {
          var lItem = new PaleteItem(item.itens[index])
          self.itens.push(lItem)
        }
      }
    }
  }

  async findPublico (pParams, recaptchatoken) {
    var self = this
    self.limpardados()
    let params = {
      ean: pParams.ean,
      senha: pParams.senha,
      hash: pParams.hash
    }

    var instance = Axios.create({
      baseURL: Vue.prototype.$configini.API_URL,
      withCredentials: false,
      headers: {
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Headers': 'Authorization',
        'Access-Control-Allow-Methods': 'GET, POST, OPTIONS, PUT, PATCH, DELETE',
        'Content-Type': 'application/json;charset=UTF-8'
      }
    })
    let ret = await instance.post('v1/publico/palete?g-recaptcha-response=' + recaptchatoken, params).then(async response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          ret.data = data.data
          ret.ok = true
          await self.cloneFrom(ret.data)
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }
}

export default Palete
