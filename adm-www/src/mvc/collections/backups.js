import Vue from 'vue'
import BackupCliente from 'src/mvc/models/backupcliente.js'
import BackupDetalhe from 'src/mvc/models/backupdetalhe.js'

class Backups {
  constructor () {
    this.limpardados()
    this.pagination = { page: 1, rowsPerPage: 20, sortBy: 'dhcoleta', descending: true, rowsNumber: 0 }
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

  async fetchDetalhe (pDoc) {
    var self = this
    let params = {
      perpage: self.pagination.rowsPerPage,
      page: self.pagination.page
    }
    if (typeof self.mesano !== 'undefined') params['mesano'] = self.mesano
    if ((self.filter !== null) && (self.filter !== '')) params['find'] = self.filter
    let ret = await Vue.prototype.$axios.get('v1/admin/backup/list/' + pDoc + '/files', { params: params }).then(response => {
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
              var backup = new BackupDetalhe(element)
              self.itens.push(backup)
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

  async fetchGroupMesAno (pDoc) {
    let params = {
    }
    let ret = await Vue.prototype.$axios.get('v1/admin/backup/list/' + pDoc + '/filespormes', { params: params }).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        ret.ok = data.ok ? data.ok : false
        ret.data = data.data
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async fetchPorCliente (pContinuos = false) {
    var self = this
    if (!pContinuos) self.limpardados()
    let params = {
      perpage: self.pagination.rowsPerPage,
      page: self.pagination.page
    }
    if (typeof self.status !== 'undefined') params['status'] = self.status
    if ((self.filter !== null) && (self.filter !== '')) params['find'] = self.filter
    if (typeof self.nivel !== 'undefined') {
      params['nivel'] = (self.nivel ? self.nivel.length > 0 : false) ? self.nivel.join(',') : ''
    }
    let ret = await Vue.prototype.$axios.get('v1/admin/backup', { params: params }).then(response => {
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
          if ((!pContinuos) || (!self.itens)) self.itens = []
          if (data.rows) {
            for (let index = 0; index < data.rows.length; index++) {
              const element = data.rows[index]
              var backup = new BackupCliente(element)
              self.itens.push(backup)
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

  async fetchDashbord () {
    // var self = this
    let params = {
    }
    let ret = await Vue.prototype.$axios.get('v1/admin/backup/dash', { params: params }).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        ret.data = data.data
        ret.ok = data.ok
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async deleteSend (pLista) {
    var ids = (pLista ? pLista.length > 0 : false) ? pLista.join(',') : ''
    let params = {
      ids: ids
    }
    let ret = await Vue.prototype.$axios.delete('v1/admin/backup/delete/files', { params: params }).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        ret.ok = data.ok
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async syncSend (pDoc) {
    let ret = await Vue.prototype.$axios.post('v1/admin/backup/list/' + pDoc + '/sync').then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        ret.ok = data.ok
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }
}
export default Backups
