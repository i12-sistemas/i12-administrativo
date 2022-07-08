import Embalagens from 'src/mvc/collections/embalagens.js'
class Embalagem {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.sigla = ''
    this.descricao = ''
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (item.sigla) self.sigla = item.sigla
    if (item.descricao) self.descricao = item.descricao
  }

  async find (pSigla) {
    var self = this
    self.limpardados()

    var lista = new Embalagens()
    for (let index = 0; index < lista.itens.length; index++) {
      const emb = lista.itens[index]
      if (emb.sigla === pSigla) {
        self.cloneFrom(emb)
        break
      }
    }
  }
}

export default Embalagem
