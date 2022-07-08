import Vue from 'vue'
import FollowupFile from 'src/mvc/models/followupfile.js'

class FollowupFiles {
  constructor (actionfilter) {
    this.limpardados()
    this.pagination = { page: 1, rowsPerPage: 20, sortBy: 'dataref', descending: false, rowsNumber: 0 }
    this.showall = false
    this.actionfilter = actionfilter ? parseInt(actionfilter) : 1
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

    var params = self.query
    params.action = self.actionfilter
    if (self.orderby !== null) params.orderby = JSON.stringify(self.orderby)

    let ret = await Vue.prototype.$axios.get('v1/followup/planilhas', { params: params }).then(response => {
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
            let p = new FollowupFile(element)
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

  async deletemass (listaIds) {
    // var self = this
    try {
      // var permissao = Vue.prototype.$helpers.permite('cadastros.clientes.delete')
      // if (!permissao.ok) return permissao
    } catch (error) {
      return { ok: false, msg: error.message }
    }
    var params = {
      ids: listaIds
    }
    let ret = await Vue.prototype.$axios.post('v1/followup/planilhas/deletemass', params).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) ret.ok = true
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }
}
export default FollowupFiles
