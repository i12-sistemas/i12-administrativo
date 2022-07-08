import Vue from 'vue'
import Configuracao from 'src/mvc/models/configuracao.js'

class Configuracoes {
  constructor () {
    this.limpardados()
  }

  async limpardados () {
    this.itens = {}
    this.data = null
  }

  additem (id, tipo, defaultvalue) {
    var c = new Configuracao(null, defaultvalue)
    c.id = id
    c.tipo = tipo
    c.valor = defaultvalue
    this.itens[c.id] = c
  }

  async fetch () {
    var self = this
    var idlist = []
    var id = null
    for (id in self.itens) {
      if (id !== '') idlist.push(id)
    }
    let params = {
      idlist: idlist.join(',')
    }
    let ret = await Vue.prototype.$axios.get('v1/configuracoes', { params: params }).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          data = data.data
          self.data = data
          for (let index = 0; index < data.rows.length; index++) {
            const element = data.rows[index]
            let p = new Configuracao(element, self.itens[element.id].defaultvalue)
            if (p.id !== '') {
              self.itens[p.id] = p
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

  async save () {
    var self = this
    try {
      var id = null
      var data = []
      for (id in self.itens) {
        var element = self.itens[id]
        if (element.id !== '') {
          if (element.oldvalue !== element.valor) {
            data.push({ id: element.id, tipo: element.tipo, valor: element.valor })
          }
        }
      }
      if (!data) throw new Error('Nenhuma configuração alterada')
      if (data.length <= 0) throw new Error('Nenhuma configuração alterada')
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }
    let params = {
      configuracoes: data
    }

    let ret = await Vue.prototype.$axios.post('v1/configuracoes', params).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          // if (data.data) self.cloneFrom(data.data)
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }
}
export default Configuracoes
