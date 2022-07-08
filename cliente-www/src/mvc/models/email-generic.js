class EmailGeneric {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.email = ''
    this.nome = ''
    this.tags = null
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (item.email) this.email = item.email
    if (item.nome) this.nome = item.nome
    if (typeof item.tags !== 'undefined') this.tags = item.tags
  }
}

export default EmailGeneric
