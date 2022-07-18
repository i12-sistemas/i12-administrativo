import Vue from 'vue'
import { Screen } from 'quasar'
import Servidor from 'src/mvc/models/servidor.js'

class Servidores {
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

  async showPrint (app, pIds) {
    let props = app.$router.resolve({
      name: 'operacional.coletas.coletas.print',
      query: { ids: pIds }
    })
    var link = props.href
    var myWindow = window.open(link, '', 'width=' + (Screen.width - 150) + 'px,height=' + (Screen.height - 40) + 'px,resizable=0,scrollbars=0,status=0,toolbar=0')
    myWindow.focus()
  }

  async print (pIds) {
    let params = {
      ids: pIds
    }
    let ret = await Vue.prototype.$axios.get('v1/painelcliente/coletas/print', { params: params }).then(response => {
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

  async showPrintListagem (app, format = 'pdf', showloading = true) {
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
    var ret = await self.export(format)
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

  async export (format = 'xlsx') {
    var self = this
    let params = {
      output: format,
      perpage: self.pagination.rowsPerPage,
      page: self.pagination.page
    }
    if ((self.filter !== null) && (self.filter !== '')) params['find'] = self.filter
    if ((self.motoristastr !== null) && (self.motoristastr !== '')) params['motoristastr'] = self.motoristastr
    if ((self.clienteorigemstr !== null) && (self.clienteorigemstr !== '')) params['clienteorigemstr'] = self.clienteorigemstr
    if ((self.regiaostr !== null) && (self.regiaostr !== '')) params['regiaostr'] = self.regiaostr
    if ((self.enderecocoletastr !== null) && (self.enderecocoletastr !== '')) params['enderecocoletastr'] = self.enderecocoletastr
    if ((self.clientedestinostr !== null) && (self.clientedestinostr !== '')) params['clientedestinostr'] = self.clientedestinostr
    if ((self.cidadedestinostr !== null) && (self.cidadedestinostr !== '')) params['cidadedestinostr'] = self.cidadedestinostr

    if (self.numero !== null) params['numero'] = self.numero
    if (self.produtosperigosos !== null) params['produtosperigosos'] = self.produtosperigosos ? 1 : 0
    if (self.veiculoexclusico !== null) params['veiculoexclusico'] = self.veiculoexclusico ? 1 : 0
    if (self.cargaurgente !== null) params['cargaurgente'] = self.cargaurgente ? 1 : 0
    if (self.semmotorista) {
      if (self.semmotorista === 'S') params['semmotorista'] = 1
    }
    if ((self.motoristas !== null) && (self.motoristas !== undefined)) params['motoristas'] = self.motoristas.join(',')
    if ((self.origem !== null) && (self.origem !== undefined)) params['origem'] = self.origem.join(',')
    if ((self.situacao !== null) && (self.situacao !== undefined)) params['situacao'] = self.situacao.join(',')

    if ((self.clientedestino !== null) && (self.clientedestino !== undefined)) params['clientedestino'] = self.clientedestino.join(',')
    if ((self.clienteorigem !== null) && (self.clienteorigem !== undefined)) params['clienteorigem'] = self.clienteorigem.join(',')
    if ((self.regiao !== null) && (self.regiao !== undefined)) params['regiao'] = self.regiao.join(',')
    if ((self.cidades !== null) && (self.cidades !== undefined)) params['cidades'] = self.cidades.join(',')
    if ((self.cidadedestino !== null) && (self.cidadedestino !== undefined)) params['cidadedestino'] = self.cidadedestino.join(',')
    if (self.dhcoletai !== null) params['dhcoletai'] = Vue.prototype.$helpers.strDateToFormated(self.dhcoletai, 'YYYY/MM/DD', 'YYYY-MM-DD')
    if (self.dhcoletaf !== null) params['dhcoletaf'] = Vue.prototype.$helpers.strDateToFormated(self.dhcoletaf, 'YYYY/MM/DD', 'YYYY-MM-DD')
    if (self.dhbaixai !== null) params['dhbaixai'] = Vue.prototype.$helpers.strDateToFormated(self.dhbaixai, 'YYYY/MM/DD', 'YYYY-MM-DD')
    if (self.dhbaixaf !== null) params['dhbaixaf'] = Vue.prototype.$helpers.strDateToFormated(self.dhbaixaf, 'YYYY/MM/DD', 'YYYY-MM-DD')
    if (self.pesoi !== null) params['pesoi'] = self.pesoi
    if (self.pesof !== null) params['pesof'] = self.pesof

    if ((typeof self.customcnpj !== 'undefined') && (self.customcnpj ? self.customcnpj !== '' : false)) params['customcnpj'] = self.customcnpj
    if ((typeof self.ctenumero !== 'undefined') && (self.ctenumero !== null)) params['ctenumero'] = self.ctenumero
    if ((typeof self.ctenumero2 !== 'undefined') && (self.ctenumero2 !== null)) params['ctenumero2'] = JSON.stringify(self.ctenumero2)

    if (self.orderby !== null) params['orderby'] = JSON.stringify(self.orderby)

    let ret = await Vue.prototype.$axios.get('v1/painelcliente/coletas/print/listagem', { params: params }).then(response => {
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
    let params = {
      perpage: self.pagination.rowsPerPage,
      page: self.pagination.page,
      ambiente: '1'
    }
    if (typeof self.ambiente !== 'undefined') params['ambiente'] = self.ambiente === '2' ? '2' : '1'
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
}
export default Servidores
