import Vue from 'vue'
import Servidor from 'src/mvc/models/servidor.js'
import BackupCliente from 'src/mvc/models/backupcliente.js'

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

  async fetch (pContinuos = false) {
    var self = this
    if (!pContinuos) self.limpardados()
    let params = {
      perpage: self.pagination.rowsPerPage,
      page: self.pagination.page
    }
    if (typeof self.status !== 'undefined') params['status'] = self.status
    if ((self.filter !== null) && (self.filter !== '')) params['find'] = self.filter
    // if ((self.regiao !== null) && (self.regiao !== undefined)) params['regiao'] = self.regiao.join(',')
    // if ((self.cidades !== null) && (self.cidades !== undefined)) params['cidades'] = self.cidades.join(',')
    // if ((self.cidadedestino !== null) && (self.cidadedestino !== undefined)) params['cidadedestino'] = self.cidadedestino.join(',')
    // if (self.dhcoletai !== null) params['dhcoletai'] = Vue.prototype.$helpers.strDateToFormated(self.dhcoletai, 'YYYY/MM/DD', 'YYYY-MM-DD')
    // if (self.dhcoletaf !== null) params['dhcoletaf'] = Vue.prototype.$helpers.strDateToFormated(self.dhcoletaf, 'YYYY/MM/DD', 'YYYY-MM-DD')
    // if (self.dhbaixai !== null) params['dhbaixai'] = Vue.prototype.$helpers.strDateToFormated(self.dhbaixai, 'YYYY/MM/DD', 'YYYY-MM-DD')
    // if (self.dhbaixaf !== null) params['dhbaixaf'] = Vue.prototype.$helpers.strDateToFormated(self.dhbaixaf, 'YYYY/MM/DD', 'YYYY-MM-DD')
    // if (self.pesoi !== null) params['pesoi'] = self.pesoi
    // if (self.pesof !== null) params['pesof'] = self.pesof

    // if ((typeof self.ctenumero !== 'undefined') && (self.ctenumero !== null)) params['ctenumero'] = self.ctenumero
    // if ((typeof self.ctenumero2 !== 'undefined') && (self.ctenumero2 !== null)) params['ctenumero2'] = JSON.stringify(self.ctenumero2)

    // if (self.orderby !== null) params['orderby'] = JSON.stringify(self.orderby)

    let ret = await Vue.prototype.$axios.get('v1/admin/servidores', { params: params }).then(response => {
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
              var servidor = new Servidor(element)
              self.itens.push(servidor)
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
    // if ((self.cidades !== null) && (self.cidades !== undefined)) params['cidades'] = self.cidades.join(',')
    // if ((self.cidadedestino !== null) && (self.cidadedestino !== undefined)) params['cidadedestino'] = self.cidadedestino.join(',')
    // if (self.dhcoletai !== null) params['dhcoletai'] = Vue.prototype.$helpers.strDateToFormated(self.dhcoletai, 'YYYY/MM/DD', 'YYYY-MM-DD')
    // if (self.dhcoletaf !== null) params['dhcoletaf'] = Vue.prototype.$helpers.strDateToFormated(self.dhcoletaf, 'YYYY/MM/DD', 'YYYY-MM-DD')
    // if (self.dhbaixai !== null) params['dhbaixai'] = Vue.prototype.$helpers.strDateToFormated(self.dhbaixai, 'YYYY/MM/DD', 'YYYY-MM-DD')
    // if (self.dhbaixaf !== null) params['dhbaixaf'] = Vue.prototype.$helpers.strDateToFormated(self.dhbaixaf, 'YYYY/MM/DD', 'YYYY-MM-DD')
    // if (self.pesoi !== null) params['pesoi'] = self.pesoi
    // if (self.pesof !== null) params['pesof'] = self.pesof

    // if ((typeof self.ctenumero !== 'undefined') && (self.ctenumero !== null)) params['ctenumero'] = self.ctenumero
    // if ((typeof self.ctenumero2 !== 'undefined') && (self.ctenumero2 !== null)) params['ctenumero2'] = JSON.stringify(self.ctenumero2)

    // if (self.orderby !== null) params['orderby'] = JSON.stringify(self.orderby)

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
      console.log(data)
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        ret.data = data.data
        ret.ok = data.ok
      }
      console.log(ret)
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }
}
export default Backups
