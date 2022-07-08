class TabelaIbptLog {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null
    this.created_at = null
    this.uf = null
    this.filename = null
    this.ip = null
    this.urlcompleta = null
    this.sizebyte = 0
    this.cnpj = null
    this.usuario = null
    this.sistema = null
    this.ambiente = null
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.id !== 'undefined') self.id = item.id
    if (typeof item.created_at !== 'undefined') self.created_at = item.created_at
    if (typeof item.uf !== 'undefined') self.uf = item.uf
    if (typeof item.filename !== 'undefined') self.filename = item.filename
    if (typeof item.ip !== 'undefined') self.ip = item.ip
    if (typeof item.urlcompleta !== 'undefined') self.urlcompleta = item.urlcompleta
    if (typeof item.sizebyte !== 'undefined') self.sizebyte = item.sizebyte
    if (typeof item.cnpj !== 'undefined') self.cnpj = item.cnpj
    if (typeof item.usuario !== 'undefined') self.usuario = item.usuario
    if (typeof item.sistema !== 'undefined') self.sistema = item.sistema
    if (typeof item.ambiente !== 'undefined') self.ambiente = item.ambiente.toString()
  }
}

export default TabelaIbptLog
