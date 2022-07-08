import Vue from 'vue'

class TabelaIbpt {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.uf = null
    this.created_at = null
    this.md5file = null
    this.filename = null
    this.sizebytes = null
    delete this.usuario
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.uf !== 'undefined') self.uf = item.uf
    if (typeof item.created_at !== 'undefined') self.created_at = item.created_at
    if (typeof item.md5file !== 'undefined') self.md5file = item.md5file
    if (typeof item.filename !== 'undefined') self.filename = item.filename
    if (typeof item.sizebytes !== 'undefined') self.sizebytes = item.sizebytes
    if (typeof item.usuario !== 'undefined') self.usuario = item.usuario
  }

  async deleteWithQuestion (app) {
    var self = this
    try {
      // var permite = Vue.prototype.$helpers.permite('operacional.expedicao.delete')
      // if (!permite.ok) throw new Error(permite.msg)
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }
    return new Promise((resolve) => {
      app.$q.dialog({
        title: 'Excluir tabela ' + self.uf + '?',
        message: 'Para excluir a tabela digite a sigla do estado ' + self.uf,
        prompt: {
          model: '',
          type: 'text' // optional
        },
        cancel: true
      }).onOk(async data => {
        if (data.toUpperCase() === self.uf.toUpperCase()) {
          var ret = await self.delete()
          resolve(ret)
        } else {
          resolve({ ok: false, msg: 'Informação inválida', warning: true })
        }
      }).onCancel(() => {
        resolve({ ok: false })
      })
    })
  }

  async delete () {
    var self = this
    const params = {
      uf: self.uf
    }
    let ret = await Vue.prototype.$axios.delete('v1/admin/tabelaibpt', { params: params }).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async download (app) {
    var self = this
    // let props = app.$router.resolve({
    //   name: 'operacional.coletas.coletas.print',
    //   query: { ids: pIds }
    // })
    // var link = props.href
    // var myWindow = window.open(link, '', 'width=' + (Screen.width - 150) + 'px,height=' + (Screen.height - 40) + 'px,resizable=0,scrollbars=0,status=0,toolbar=0')
    // myWindow.focus()

    var link = Vue.prototype.$configini.API_URL + 'v1/admin/tabelaibpt/download/' + self.uf
    app.$helpers.forceFileDownload(link)
  }
}

export default TabelaIbpt
