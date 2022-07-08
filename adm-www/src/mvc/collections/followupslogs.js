import Vue from 'vue'
import FollowUpLog from 'src/mvc/models/followuplog.js'
// import { Screen } from 'quasar'

import EditorRapidoDialog from 'src/pages/comercial/followup/editores/cpn-editor-rapido-dialog'
class FollowUpsLog {
  constructor (pIDFUP) {
    this.fupid = pIDFUP
    this.limpardados()
    this.pagination = { page: 1, rowsPerPage: 20, sortBy: 'created_at', descending: true, rowsNumber: 0 }
  }

  async limpardados () {
    this.itens = null
    this.counters = null
  }

  readPropsTable (props) {
    if (!props) return
    const { page, rowsPerPage, sortBy, descending, rowsNumber } = props.pagination
    const filter = props.filter
    if (this.filter !== filter) this.page = 1
    this.pagination = { page: page, rowsPerPage: rowsPerPage, sortBy: sortBy, descending: descending, rowsNumber: rowsNumber }
    this.filter = filter
  }

  async fetch (pIDFUP) {
    var self = this
    var id = self.fupid
    if (parseInt(pIDFUP) > 0) id = pIDFUP
    self.limpardados()

    let ret = await Vue.prototype.$axios.get('v1/followup/' + id + '/log', { params: self.query }).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          data = data.data
          self.total = data.total ? parseInt(data.total) : 0
          // don't forget to update local pagination object
          self.pagination.page = data.current_page
          self.pagination.rowsPerPage = parseInt(data.per_page)
          self.pagination.sortBy = data.sortby ? data.sortby : ''
          self.pagination.descending = (data.descending === 'desc')
          self.pagination.rowsNumber = data.total ? parseInt(data.total) : 0
          ret.ok = true
          if (!self.itens) self.itens = []
          if (data.counters) self.counters = data.counters
          if (data.rows) {
            for (let index = 0; index < data.rows.length; index++) {
              const element = data.rows[index]
              let p = new FollowUpLog(element)
              if (p.id > 0) self.itens.push(p)
            }
          }
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async showExportListagem (id, app, format = 'xlsx', showloading = true) {
    var self = this
    if (showloading) {
      var dialog = app.$q.dialog({
        message: 'Preparando documento ' + format + ', aguarde...',
        progress: true, // we enable default settings
        color: 'blue',
        persistent: true, // we want the user to not be able to close it
        ok: false // we want the user to not be able to close it
      })
    }
    var ret = await self.exportFile(id, format)
    if (showloading) dialog.hide()
    if (ret.ok) {
      Vue.prototype.$helpers.forceFileDownload(ret.msg)
    } else {
      if (showloading) {
        if (ret.msg ? ret.msg !== '' : false) {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {})
        }
      }
    }
    return ret
  }

  async exportFile (pIDFUP, output) {
    var self = this
    var id = self.fupid
    if (parseInt(pIDFUP) > 0) id = pIDFUP
    var params = self.query
    params.output = output

    let ret = await Vue.prototype.$axios.get('v1/followup/' + id + '/log', { params: self.query }).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        ret.ok = data.ok ? data.ok : false
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async showEditorRapido (app, pLista) {
    // var self = this

    try {
      // var permite = Vue.prototype.$helpers.permite('operacional.coletas.save')
      // if (!permite.ok) throw new Error(permite.msg)
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }

    return new Promise((resolve) => {
      app.$q.dialog({
        parent: app,
        component: EditorRapidoDialog,
        lista: pLista,
        cancel: true
      }).onOk(async data => {
        resolve({ ok: data })
      }).onCancel(() => {
        resolve({ ok: false })
      })
    })
  }
}
export default FollowUpsLog
