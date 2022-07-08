import Vue from 'vue'
import Cidade from 'src/mvc/models/cidade.js'

class Cidades {
  constructor () {
    this.limpardados()
    this.pagination = { page: 1, rowsPerPage: 20, sortBy: 'nome', descending: false, rowsNumber: 0 }
    this.showall = false
    // this.store = new CustomStore({
    //   key: 'id',
    //   load: async (loadOptions) => {
    //     var self = this
    //     // var filter = []
    //     var list = ['filter', 'group', 'groupSummary', 'parentIds', 'requireGroupCount', 'requireTotalCount', 'searchExpr',
    //       'searchOperation', 'searchValue', 'select', 'sort', 'skip', 'take', 'totalSummary', 'userData']
    //     list.forEach(function (i) {
    //       if ((i in loadOptions) && !Vue.prototype.$helpers.isNotEmpty(loadOptions[i])) {
    //       }
    //     })
    //     self.pagination.rowsPerPage = loadOptions.take
    //     self.pagination.page = (parseInt(loadOptions.skip / loadOptions.take) + 1)
    //     await this.fetch('')
    //     return {
    //       data: this.itens,
    //       totalCount: this.pagination.rowsNumber
    //       // summary: response.summary,
    //       // groupCount: response.groupCount
    //     }
    //   },
    //   insert: (values) => {
    //   },
    //   update: (key, values) => {
    //   },
    //   remove: (key) => {
    //   }
    // })
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

  async fetch (BuscarSomentePorCodigo) {
    var self = this
    self.limpardados()
    let params = {
      find: self.filter,
      perpage: self.pagination.rowsPerPage,
      page: self.pagination.page,
      sortby: self.pagination.sortBy,
      descending: (self.pagination.descending === true)
    }
    if (self.ids) {
      if (self.ids !== null) params['ids'] = self.ids.join(',')
    }
    if (self.showall) {
      params['showall'] = self.showall ? 1 : 0
    }

    if (BuscarSomentePorCodigo) params['buscarporcodigo'] = true

    let ret = await Vue.prototype.$axios.get('v1/cidade', { params: params }).then(async response => {
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
            var p = new Cidade()
            await p.cloneFrom(element)
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
export default Cidades
