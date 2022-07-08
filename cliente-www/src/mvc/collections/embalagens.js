import Embalagem from 'src/mvc/models/embalagem.js'

class Embalagens {
  constructor () {
    this.limpardados()
    this.pagination = { page: 1, rowsPerPage: 20, sortBy: 'nome', descending: false, rowsNumber: 0 }
  }

  async limpardados () {
    this.itens = []

    var emb = new Embalagem()
    emb.sigla = 'LITROS'
    emb.descricao = 'Litros'
    this.itens.push(emb)

    emb = new Embalagem()
    emb.sigla = 'M3'
    emb.descricao = 'Metros Cúbico'
    this.itens.push(emb)

    emb = new Embalagem()
    emb.sigla = 'QUILOS'
    emb.descricao = 'Quilos'
    this.itens.push(emb)
  }
}
export default Embalagens
