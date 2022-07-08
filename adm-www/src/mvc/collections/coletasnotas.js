import Vue from 'vue'
import ColetaNota from 'src/mvc/models/coletanota.js'
import DialogConsultaImport from 'src/pages/operacional/coletasnotas/consulta-import-dialog.vue'

class ColetasNotas {
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
    let params = {
      perpage: self.pagination.rowsPerPage,
      page: self.pagination.page
    }
    if (self.ids) {
      if (self.ids !== null) params['ids'] = self.ids.join(',')
    }
    if (self.filter) {
      if (self.filter !== '') params['find'] = self.filter
    }
    if (self.dhlocal_datai !== null) params['dhlocal_datai'] = Vue.prototype.$helpers.strDateToFormated(self.dhlocal_datai, 'YYYY/MM/DD', 'YYYY-MM-DD')
    if (self.dhlocal_dataf !== null) params['dhlocal_dataf'] = Vue.prototype.$helpers.strDateToFormated(self.dhlocal_dataf, 'YYYY/MM/DD', 'YYYY-MM-DD')
    if (self.remetentenome !== null) params['remetentenome'] = self.remetentenome
    if (self.remetentecnpj !== null) params['remetentecnpj'] = self.remetentecnpj
    if (self.destinatarionome !== null) params['destinatarionome'] = self.destinatarionome
    if (self.destinatariocnpj !== null) params['destinatariocnpj'] = self.destinatariocnpj
    if (self.motorista !== null) params['motorista'] = self.motorista
    if (self.id !== null) params['id'] = self.id
    if (self.idcoleta !== null) params['idcoleta'] = self.idcoleta
    if (self.chave !== null) params['chave'] = self.chave
    if (self.notanumero !== null) params['notanumero'] = self.notanumero
    if (self.coletaavulsa !== null) params['coletaavulsa'] = self.coletaavulsa
    if (self.status !== null) params['status'] = JSON.stringify(self.status)
    if (typeof self.omitircomcargaentrada !== 'undefined') {
      if (self.omitircomcargaentrada) params['omitircomcargaentrada'] = 1
    }
    if (self.orderby !== null) params['orderby'] = JSON.stringify(self.orderby)

    let ret = await Vue.prototype.$axios.get('v1/painelcliente/coletasnotas', { params: params }).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          data = data.data
          self.total = data.total ? parseInt(data.total) : 0
          // don't forget to update local pagination object
          this.pagination.page = data.current_page
          this.pagination.rowsPerPage = data.per_page
          // this.pagination.sortBy = data.sortby ? data.sortby : ''
          this.pagination.descending = (data.descending === true)
          this.pagination.rowsNumber = data.total ? parseInt(data.total) : 0

          ret.ok = true
          self.itens = []
          for (let index = 0; index < data.rows.length; index++) {
            const element = data.rows[index]
            let p = new ColetaNota(element)
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

  async showConsultaImport (app, startFilter = null) {
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
        component: DialogConsultaImport,
        dataset: self,
        startFilter: startFilter,
        cancel: true
      }).onOk(async data => {
        resolve({ ok: true, data: data })
      }).onCancel(() => {
        resolve({ ok: false })
      })
    })
  }
}
export default ColetasNotas
