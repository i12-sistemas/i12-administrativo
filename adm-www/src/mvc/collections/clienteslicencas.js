import Vue from 'vue'
import ClienteLicenca from 'src/mvc/models/clientelicenca.js'
import adddialog from 'src/pages/cliente/licenca/licencaadd-dialog'

class ClientesLicencas {
  constructor () {
    this.limpardados()
    this.pagination = { page: 1, rowsPerPage: 20, sortBy: 'razao', descending: false, rowsNumber: 0 }
    this.showall = false
  }

  async limpardados () {
    this._last_page = 1
    this.itens = null
  }

  readPropsTable (props, resetPage = false) {
    if (resetPage) {
      if (this.pagination) this.pagination.page = 1
    }
    if (!props) return
    const { page, rowsPerPage, sortBy, descending, rowsNumber } = props.pagination
    const filter = props.filter
    if (this.filter !== filter) this.page = 1
    this.pagination = { page: page, rowsPerPage: rowsPerPage, sortBy: sortBy, descending: descending, rowsNumber: rowsNumber }
    this.filter = filter
  }

  get temmaisregistros () {
    return (this.currentpage !== this.lastpage) && (this.itens ? this.itens.length > 0 : false)
  }

  get totalrecordcount () {
    if (this.pagination) {
      return this.pagination.rowsNumber ? this.pagination.rowsNumber : 0
    } else {
      return 0
    }
  }

  get recordcount () {
    return (this.itens ? this.itens.length : 0)
  }

  get currentpage () {
    if (this.pagination) {
      return this.pagination.page ? this.pagination.page : 1
    } else {
      return 1
    }
  }
  get lastpage () {
    return this._last_page ? this._last_page : 1
  }

  get infopagination () {
    return this.recordcount + ' de ' + this.totalrecordcount
  }

  async makequery () {
    var self = this
    try {
      var query = {}
      query.t = new Date().getTime()
      if (self.pagination.page !== null && self.pagination.page > 1) query.page = self.pagination.page
      if ((self.pagination.rowsPerPage !== null) && (self.pagination.rowsPerPage > 0) && (parseInt(self.pagination.rowsPerPage) !== 20)) query.pagesize = self.pagination.rowsPerPage
      if (self.orderby !== null) query.sortby = JSON.stringify(self.orderby)

      if (self.paruams) {
        for (var prop in self.params) {
          var value = self.params[prop]
          if (value !== null && value !== '') query[prop] = value
        }
      }
      return query
    } catch (error) {
      return null
    }
  }

  async loadmore () {
    this.pagination.page = parseInt(this.currentpage) + 1
    var ret = await this.fetch(true)
    return ret
  }

  async fetch (pContinuos = false) {
    var self = this
    if (!pContinuos) self.limpardados()
    let params = {
      showall: self.showall ? 1 : 0,
      perpage: self.pagination.rowsPerPage,
      page: self.pagination.page
    }
    if (self.status) {
      if (self.status !== null) params['status'] = self.status
    }
    if (self.filter) {
      if ((self.filter !== null) && (self.filter !== '')) params['find'] = self.filter
    }

    if (self.params) {
      for (var prop in self.params) {
        var value = self.params[prop]
        if (value !== null && value !== '') params[prop] = value
      }
    }

    if (self.orderby !== null) params['orderby'] = JSON.stringify(self.orderby)

    let ret = await Vue.prototype.$axios.get('v1/admin/clientes/licencas', { params: params }).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          data = data.data
          self.total = data.total ? parseInt(data.total) : 0
          // don't forget to update local pagination object
          this.pagination.page = data.current_page
          this._last_page = data.last_page
          this.pagination.rowsPerPage = data.per_page
          // this.pagination.sortBy = data.sortby ? data.sortby : ''
          this.pagination.descending = (data.descending === true)
          this.pagination.rowsNumber = data.total ? parseInt(data.total) : 0

          ret.ok = true
          if ((!pContinuos) || (!self.itens)) self.itens = []
          for (let index = 0; index < data.rows.length; index++) {
            const element = data.rows[index]
            let p = new ClienteLicenca(element)
            self.itens.push(p)
          }
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async showAddLicenca (app, pCliente) {
    return new Promise((resolve) => {
      app.$q.dialog({
        parent: app,
        component: adddialog,
        cliente: pCliente,
        cancel: true
      }).onOk(async retOk => {
        resolve({ ok: true, dados: retOk })
      }).onCancel(() => {
        resolve(null)
      })
    })
  }
}
export default ClientesLicencas
