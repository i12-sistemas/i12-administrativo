class XMLPendente {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.notanumero = null
    this.notaserie = null
    this.notachave = null
    this.dhultimo = null
    this.file = null
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.notanumero !== 'undefined') this.notanumero = item.notanumero
    if (typeof item.notaserie !== 'undefined') this.notaserie = item.notaserie
    if (typeof item.notachave !== 'undefined') this.notachave = item.notachave
    if (typeof item.dhultimo !== 'undefined') this.dhultimo = item.dhultimo
  }
}

export default XMLPendente
