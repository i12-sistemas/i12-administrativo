import Vue from 'vue'
// import NFe from 'src/mvc/models/nfe.js'

class NFes {
  constructor () {
    this.limpardados()
    this.pagination = { page: 1, rowsPerPage: 20, sortBy: 'nome', descending: false, rowsNumber: 0 }
  }

  async limpardados () {
    this.itens = null
  }

  readPropsTable (props) {
    if (!props) return
    const { page, rowsPerPage, sortBy, descending, rowsNumber } = props.pagination
    const filter = props.filter
    if (this.filter !== filter) this.page = 1
    this.pagination = { page: page, rowsPerPage: rowsPerPage, sortBy: sortBy, descending: descending, rowsNumber: rowsNumber }
    this.filter = filter
  }

  async fetch () {
    var self = this
    self.limpardados()
    try {
      var permite = Vue.prototype.$helpers.permite('comercial.nfe.consulta')
      if (!permite.ok) throw new Error(permite.msg)
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }
    let params = self.query
    let ret = await Vue.prototype.$axios.get('v1/nfe/consulta', { params: params }).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          // data = data.data
          // self.total = data.total ? parseInt(data.total) : 0
          // // don't forget to update local pagination object
          // this.pagination.page = data.current_page
          // this.pagination.rowsPerPage = data.per_page
          // // this.pagination.sortBy = data.sortby ? data.sortby : ''
          // this.pagination.descending = (data.descending === true)
          // this.pagination.rowsNumber = data.total ? parseInt(data.total) : 0

          // ret.ok = true
          // self.itens = []
          // for (let index = 0; index < data.rows.length; index++) {
          //   self.itens.push(data.rows[index])
          // }
          ret.msg = data.msg ? data.msg : ''
          ret.ok = data.ok ? data.ok : false
          if (data.data) {
            self.danfe = data.data
            ret.data = data.data
          }
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async export (app, format = 'xlsx') {
    var self = this
    var dialog = app.$q.dialog({
      message: 'Preparando documento ' + format + ', aguarde...',
      progress: true, // we enable default settings
      color: 'blue',
      persistent: true, // we want the user to not be able to close it
      ok: false // we want the user to not be able to close it
    })
    var ret = await self.getExport(format)
    dialog.hide()
    if (ret.ok) {
      Vue.prototype.$helpers.forceFileDownload(ret.msg)
    } else {
      if (ret.msg ? ret.msg !== '' : false) {
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {})
      }
    }
    return ret
  }

  async getExport (format = 'xlsx') {
    var self = this
    let params = self.query
    params['output'] = format
    let ret = await Vue.prototype.$axios.get('v1/nfe/consulta', { params: params }).then(response => {
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
}
export default NFes
