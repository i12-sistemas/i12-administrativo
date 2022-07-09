import Vue from 'vue'

class DatabaseCliente {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null
    this.cnpj = null
    this.nome = null
    this.cnpjoperacional = false
    this.cnpjsecundario = false
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.id !== 'undefined') self.id = item.id
    if (typeof item.cnpj !== 'undefined') self.cnpj = item.cnpj
    if (typeof item.nome !== 'undefined') self.nome = item.nome
    if (typeof item.cnpjoperacional !== 'undefined') self.cnpjoperacional = Vue.prototype.$helpers.toBool(item.cnpjoperacional)
    if (typeof item.cnpjsecundario !== 'undefined') self.cnpjsecundario = Vue.prototype.$helpers.toBool(item.cnpjsecundario)
  }
}

export default DatabaseCliente
