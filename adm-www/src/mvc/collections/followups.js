import Vue from 'vue'
// import { Screen } from 'quasar'
import FollowUp from 'src/mvc/models/followup.js'
import DialogAddOrEdit from 'src/components/cpn-filter-datetime-interval.vue'
class FollowUps {
  constructor () {
    this.limpardados()
    this.pagination = { page: 1, rowsPerPage: 20, sortBy: 'datasolicitacao', descending: true, rowsNumber: 0 }
  }

  async limpardados () {
    this.itens = null
    this.counters = null
    this.dateinicial = null
    this.datefinal = null
  }

  readPropsTable (props) {
    if (!props) return
    const { page, rowsPerPage, sortBy, descending, rowsNumber } = props.pagination
    this.pagination = { page: page, rowsPerPage: rowsPerPage, sortBy: sortBy, descending: descending, rowsNumber: rowsNumber }
  }

  async savemass (pLista) {
    // var self = this
    try {
      // var permite = Vue.prototype.$helpers.permite('operacional.coletas.save')
      // if (!permite.ok) throw new Error(permite.msg)
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }

    try {
      var data = []
      for (let index = 0; index < pLista.length; index++) {
        const row = pLista[index]
        var item = { id: row.id }

        if (typeof row.datasolicitacao !== 'undefined') item.datasolicitacao = row.datasolicitacao
        if (typeof row.dataagendamentocoleta !== 'undefined') item.dataagendamentocoleta = row.dataagendamentocoleta
        if (typeof row.erroagendastatus !== 'undefined') item.erroagendastatus = row.erroagendastatus.value
        if (typeof row.iniciofollowup !== 'undefined') item.iniciofollowup = row.iniciofollowup.value
        if (typeof row.errodtpromessastatus !== 'undefined') item.errodtpromessastatus = row.errodtpromessastatus.value
        if (typeof row.observacao !== 'undefined') item.observacao = row.observacao
        if (typeof row.dataconfirmacao !== 'undefined') item.dataconfirmacao = row.dataconfirmacao
        if (typeof row.statusconfirmacaocoleta !== 'undefined') item.statusconfirmacaocoleta = row.statusconfirmacaocoleta.value
        if (typeof row.notafiscal !== 'undefined') item.notafiscal = row.notafiscal
        if (typeof row.coletaid !== 'undefined') item.coletaid = row.coletaid
        if (typeof row.datacoleta !== 'undefined') item.datacoleta = row.datacoleta
        if (typeof row.errocoletastatus !== 'undefined') item.errocoletastatus = row.errocoletastatus.value

        if (typeof row.erroagendastatus !== 'undefined') {
          if (row.erroagendastatus.value === '2') {
            if (!row.erroagenda) throw new Error('Obrigatório informar o erro do status do agendamento')
            if (!row.erroagenda.id) throw new Error('Obrigatório informar o erro do status do agendamento')
            if (!(row.erroagenda.id > 0)) throw new Error('Obrigatório informar o erro do status do agendamento')
            item.erroagendaid = row.erroagenda.id
          }
        }
        if (typeof row.errodtpromessastatus !== 'undefined') {
          if (row.errodtpromessastatus.value === '2') {
            if (!row.errodtpromessa) throw new Error('Obrigatório informar o erro do status do "Agendamento até promessa"')
            if (!row.errodtpromessa.id) throw new Error('Obrigatório informar o erro do status do "Agendamento até promessa"')
            if (!(row.errodtpromessa.id > 0)) throw new Error('Obrigatório informar o erro do status do "Agendamento até promessa"')
            item.errodtpromessaid = row.errodtpromessa.id
          }
        }
        if (typeof row.errocoletastatus !== 'undefined') {
          if (row.errocoletastatus.value === '2') {
            if (!row.errocoleta) throw new Error('Obrigatório informar o erro do status da Coleta')
            if (!row.errocoleta.id) throw new Error('Obrigatório informar o erro do status da Coleta')
            if (!(row.errocoleta.id > 0)) throw new Error('Obrigatório informar o erro do status da Coleta')
            item.errocoletaid = row.errocoleta.id
          }
        }
        data.push(item)
      }
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }

    let params = {
      data: data
    }
    let ret = await Vue.prototype.$axios.post('v1/followup/updatemass', params).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          if (data.data) ret.data = data.data
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async dialogAddOrEdit (app) {
    return new Promise((resolve) => {
      app.$q.dialog({
        parent: app,
        component: DialogAddOrEdit,
        dataset: self,
        adding: true,
        cancel: true
      }).onOk(async retDados => {
        var ret = retDados
        this.dateinicial = ret.valuei
        this.datefinal = ret.valuef
        resolve(ret)
      }).onCancel(() => {
        resolve({ ok: false })
      })
    })
  }

  async showPrintListagem (app, format = 'pdf', showloading = true, visiblecolumns) {
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
    var ret = await self.export(format, visiblecolumns)
    if (showloading) dialog.hide()
    if (ret.ok) {
      if (format === 'pdf') {
        Vue.prototype.$helpers.showPrint(ret.msg)
      } else {
        Vue.prototype.$helpers.forceFileDownload(ret.msg)
      }
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

  async export (format = 'xlsx', visiblecolumns) {
    var self = this
    let params = self.query
    delete params.perpage
    delete params.page
    params.visiblecolumns = visiblecolumns.join(',')
    params.output = format
    if (self.orderby !== null) params.orderby = JSON.stringify(self.orderby)

    let ret = await Vue.prototype.$axios.get('v1/followup/print/listagem', { params: params }).then(response => {
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

  async fetch (pContinuos = false) {
    var self = this
    if (!pContinuos) self.limpardados()
    if (self.orderby !== null) self.query.orderby = JSON.stringify(self.orderby)

    let ret = await Vue.prototype.$axios.get('v1/painelcliente/followup', { params: self.query }).then(response => {
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
          if (data.counters) self.counters = data.counters
          if (data.rows) {
            for (let index = 0; index < data.rows.length; index++) {
              const element = data.rows[index]
              let p = new FollowUp(element)
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

  async fetchlistcompradores () {
    let ret = await Vue.prototype.$axios.get('v1/followup/list/compradores', {}).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '', data: null }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        ret.ok = data.ok ? data.ok : false
        ret.data = data.data ? data.data : null
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async fetchlistitensdescricao (pStartWith, pIDS) {
    var params = {}
    if (pStartWith !== '') params['find'] = pStartWith
    if (pIDS) params['ids'] = JSON.stringify(pIDS)

    let ret = await Vue.prototype.$axios.get('v1/followup/list/itemdescricao', { params: params }).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '', data: null }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          data = data.data
          ret.ok = true
          if (data.rows) {
            ret.data = []
            for (let index = 0; index < data.rows.length; index++) {
              ret.data.push(data.rows[index])
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

  async fetchlistitemid (pStartWith, pIDS) {
    var params = {}
    if (pStartWith !== '') params['find'] = pStartWith
    if (pIDS) params['ids'] = JSON.stringify(pIDS)

    let ret = await Vue.prototype.$axios.get('v1/followup/list/itemid', { params: params }).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '', data: null }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          data = data.data
          ret.ok = true
          if (data.rows) {
            ret.data = []
            for (let index = 0; index < data.rows.length; index++) {
              ret.data.push(data.rows[index])
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

  async fetchDashboard1 (app) {
    let params = {
      dti: app.dateinicial,
      dtf: app.datefinal
    }
    let ret = await Vue.prototype.$axios.get('v1/painelcliente/followup/dashboard/1', { params }).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '', data: null }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        ret.ok = data.ok ? data.ok : false
        ret.data = data.data ? data.data : null
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }
}
export default FollowUps
