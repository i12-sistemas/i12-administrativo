import Vue from 'vue'
import TabelaIbptLog from 'src/mvc/models/tabelaibptlog.js'

class TabelasIbptLogs {
  constructor () {
    this.limpardados()
    this.pagination = { page: 1, rowsPerPage: 25, sortBy: 'created_at', descending: true, rowsNumber: 0 }
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

  async loadmore () {
    this.pagination.page = parseInt(this.pagination.page) + 1
    var ret = await this.fetch(true)
    return ret
  }

  async fetch () {
    var self = this
    let params = {
      perpage: self.pagination.rowsPerPage,
      page: self.pagination.page
    }
    self.itens = []
    let ret = await Vue.prototype.$axios.get('v1/admin/tabelaibpt/logs', { params: params }).then(response => {
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
          self.itens = []
          if (data.rows) {
            for (let index = 0; index < data.rows.length; index++) {
              const element = data.rows[index]
              let p = new TabelaIbptLog(element)
              self.itens.push(p)
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
}
export default TabelasIbptLogs
