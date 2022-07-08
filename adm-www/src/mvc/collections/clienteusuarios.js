import Vue from 'vue'
import ClienteUsuario from 'src/mvc/models/clienteusuario.js'

class ClienteUsuarios {
  constructor () {
    this.limpardados()
    this.pagination = { page: 1, rowsPerPage: 20, sortBy: 'nome', descending: false, rowsNumber: 0 }
    this.showall = false
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

  async fetchPorCliente (pIDCliente) {
    var self = this
    self.limpardados()
    let params = {
      showall: self.showall ? 1 : 0
      // perpage: self.pagination.rowsPerPage,
      // page: self.pagination.page,
      // sortby: self.pagination.sortBy,
      // descending: self.pagination.descending ? 'desc' : 'asc'
    }
    if (typeof pIDCliente !== 'undefined') {
      if (pIDCliente > 0) params.clienteid = pIDCliente
    }
    let ret = await Vue.prototype.$axios.get('/v1/painelcliente/usuarios', { params: params }).then(response => {
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
          for (let index = 0; index < data.rows.length; index++) {
            const element = data.rows[index]
            let p = new ClienteUsuario(element)
            if (p.id > 0) self.itens.push(p)
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
export default ClienteUsuarios
