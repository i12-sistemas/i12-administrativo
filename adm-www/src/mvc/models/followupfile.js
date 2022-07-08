import Usuario from 'src/mvc/models/usuario.js'
import moment from 'moment'
import Vue from 'vue'
import { PlanilhaStatusProcessamento, FileFupTipoExport } from './enums/followuptypes'
// import DialogAddOrEdit from 'src/pages/cadastro/clientes/editdialog.vue'
import DialogLogView from 'src/pages/comercial/followup/planilhas/cpn-import-logview-dialog.vue'

class FollowupFile {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null
    this.file = null // sempre null usado somente para cadastro
    this.dataref = moment().format('YYYY-MM-DD')
    this.dataref2 = moment().format('YYYY-MM-DD')
    this.processado = new PlanilhaStatusProcessamento(0)
    this.nomeoriginal = ''
    this.tempoprocessamento = 0
    this.numlinhas = 0
    this.numerros = 0
    this.action = 1
    this.tipoexport = null
    this.size = 0
    this.log = null
    this.link = null
    this.storageurl = null
    this.processostart = null
    this.processoend = null
    this.created_at = null
    this.created_usuario = new Usuario()
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (item.id) this.id = item.id
    this.dataref = item.dataref
    this.dataref2 = item.dataref2
    if (item.nomeoriginal) this.nomeoriginal = item.nomeoriginal
    if (typeof item.link !== 'undefined') this.link = item.link
    if (typeof item.tipoexport !== 'undefined') this.tipoexport = new FileFupTipoExport(item.tipoexport)
    if (typeof item.action !== 'undefined') this.action = item.action
    if (typeof item.numlinhas !== 'undefined') this.numlinhas = item.numlinhas
    if (typeof item.numerros !== 'undefined') this.numerros = item.numerros
    if (typeof item.processado !== 'undefined') this.processado.value = item.processado
    if (typeof item.log !== 'undefined') this.log = item.log
    if (typeof item.tempoprocessamento !== 'undefined') this.tempoprocessamento = item.tempoprocessamento
    if (typeof item.size !== 'undefined') this.size = item.size
    if (item.storageurl) this.storageurl = item.storageurl
    if (item.fantasia_followup) this.fantasia_followup = item.fantasia_followup
    if (item.created_at) this.created_at = item.created_at
    if (typeof item.processostart !== 'undefined') self.processostart = item.processostart
    if (typeof item.processoend !== 'undefined') self.processoend = item.processoend
    if (typeof item.created_usuario !== 'undefined') self.created_usuario = new Usuario(item.created_usuario)
  }

  // async find (pID) {
  //   var self = this
  //   self.limpardados()
  //   let ret = await Vue.prototype.$axios.get('v1/cliente/' + pID).then(async response => {
  //     let data = response.data
  //     var ret = { ok: false, msg: '' }
  //     if (data) {
  //       ret.msg = data.msg ? data.msg : ''
  //       if (data.ok) {
  //         ret.ok = true
  //         await self.cloneFrom(data.data)
  //       }
  //     }
  //     return ret
  //   }).catch(error => {
  //     return Vue.prototype.$helpers.errorReturn(error)
  //   })
  //   return ret
  // }

  async addFile () {
    var self = this
    try {
      // var permite = Vue.prototype.$helpers.permite('cadastros.clientes.save')
      // if (!permite.ok) throw new Error(permite.msg)
    } catch (error) {
      return { ok: false, msg: error.message }
    }
    var formData = new FormData()
    formData.append('dataref', self.dataref)
    formData.append('arquivo', self.file)
    var instance = Vue.prototype.$axios
    instance.defaults.headers.common['Content-Type'] = 'multipart/form-data'
    let ret = await instance.post('v1/followup/planilhas/addfile', formData).then(async response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (typeof data.ok !== 'undefined') ret.ok = data.ok
        if (typeof data.data !== 'undefined') await self.cloneFrom(data.data)
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async exportFile () {
    var self = this
    try {
      // var permite = Vue.prototype.$helpers.permite('cadastros.clientes.save')
      // if (!permite.ok) throw new Error(permite.msg)
    } catch (error) {
      return { ok: false, msg: error.message }
    }
    var params = {
      dti: Vue.prototype.$helpers.strToMoment(self.dataref).format('YYYY-MM-DD'),
      dtf: Vue.prototype.$helpers.strToMoment(self.dataref2).format('YYYY-MM-DD'),
      tipoexport: self.tipoexport
    }
    let ret = await Vue.prototype.$axios.post('v1/followup/planilhas/export', params).then(async response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (typeof data.ok !== 'undefined') ret.ok = data.ok
        if (typeof data.data !== 'undefined') await self.cloneFrom(data.data)
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async deleteWithQuestion (app) {
    var self = this
    try {
      // var permite = Vue.prototype.$helpers.permite('cadastros.clientes.delete')
      // if (!permite.ok) throw new Error(permite.msg)
    } catch (error) {
      return { ok: false, msg: error.message }
    }
    return new Promise((resolve) => {
      app.$q.dialog({
        title: 'Excluir arquivo?',
        message: 'Para excluir o arquivo ' + self.nomeoriginal + ' digite o código ' + self.id + '?',
        prompt: {
          model: '',
          type: 'text' // optional
        },
        cancel: true
      }).onOk(async data => {
        if (parseInt(data) === parseInt(self.id)) {
          var ret = await self.delete()
          resolve(ret)
        } else {
          resolve({ ok: false, msg: 'Informação inválida', warning: true })
        }
      }).onCancel(() => {
        resolve({ ok: false })
      })
    })
  }

  async delete () {
    var self = this
    try {
      // var permissao = Vue.prototype.$helpers.permite('cadastros.clientes.delete')
      // if (!permissao.ok) return permissao
    } catch (error) {
      return { ok: false, msg: error.message }
    }
    let ret = await Vue.prototype.$axios.delete('v1/followup/planilhas/' + self.id).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          self.limpardados()
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async showLogView (app) {
    var self = this

    try {
      // var permite = Vue.prototype.$helpers.permite('operacional.coletas.save')
      // if (!permite.ok) throw new Error(permite.msg)
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }

    return new Promise((resolve) => {
      app.$q.dialog({
        parent: app,
        component: DialogLogView,
        dataset: self,
        cancel: true
      }).onOk(async data => {
        resolve({ ok: data })
      }).onCancel(() => {
        resolve({ ok: false })
      })
    })
  }
}

export default FollowupFile
