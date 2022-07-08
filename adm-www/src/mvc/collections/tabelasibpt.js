import Vue from 'vue'
import TabelaIbpt from 'src/mvc/models/tabelaibpt.js'
import adddialog from 'src/pages/tabelaibpt/add-dialog'

class TabelasIbpt {
  constructor () {
    this.limpardados()
    this.pagination = { page: 1, rowsPerPage: 15, sortBy: 'dhcoleta', descending: true, rowsNumber: 0 }
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
      // perpage: self.pagination.rowsPerPage,
      // page: self.pagination.page
    }
    self.itens = []
    let ret = await Vue.prototype.$axios.get('v1/admin/tabelaibpt', { params: params }).then(response => {
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
              let p = new TabelaIbpt(element)
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

  async upload (file, uf, semestre, ano) {
    var formData = new FormData()
    formData.append('uf', uf)
    formData.append('semestre', semestre)
    formData.append('ano', ano)
    formData.append('arquivo', file)
    var instance = Vue.prototype.$axios
    instance.defaults.headers.common['Content-Type'] = 'multipart/form-data'
    let ret = await instance.post('v1/admin/tabelaibpt/upload', formData).then(async response => {
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

  async showAdd (app) {
    return new Promise((resolve) => {
      app.$q.dialog({
        parent: app,
        component: adddialog,
        cancel: true
      }).onOk(async retOk => {
        resolve({ ok: true, dados: retOk })
      }).onCancel(() => {
        resolve(null)
      })
    })
  }
}
export default TabelasIbpt
