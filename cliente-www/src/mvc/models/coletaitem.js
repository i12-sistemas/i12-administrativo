import Usuario from 'src/mvc/models/usuario.js'
import Produto from 'src/mvc/models/produto.js'
import Embalagem from 'src/mvc/models/embalagem.js'
// import Vue from 'vue'

class ColetaItem {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null

    this.produto = new Produto()
    this.produtoclassificado = false
    this.embalagem = new Embalagem()
    this.qtde = 0

    this.created_at = null
    this.created_usuario = new Usuario()
    this.updated_at = null
    this.updated_usuario = new Usuario()
  }

  async sendData () {
    var data = {
      id: this.id,
      produtoid: this.produto.id,
      produtonome: this.produto.nome,
      embalagem: this.embalagem.sigla,
      qtde: this.qtde
    }
    if (this.deleted) data.deleted = this.deleted
    if (this.updated) data.updated = this.updated
    if (this.inserted) data.inserted = this.inserted
    return data
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (item.id) this.id = item.id
    if (item.embalagem) {
      if (item.embalagem instanceof Embalagem) {
        self.embalagem.cloneFrom(item.embalagem)
      } else {
        self.embalagem.find(item.embalagem)
      }
    }
    if (item.qtde) this.qtde = parseFloat(item.qtde)

    if (item.produto) await self.produto.cloneFrom(item.produto)
    self.produtoclassificado = item.produto ? (item.produto.id > 0) : false

    if (item.created_at) self.created_at = item.created_at
    if (item.created_usuario) await self.created_usuario.cloneFrom(item.created_usuario)

    if (item.updated_at) self.updated_at = item.updated_at
    if (item.updated_usuario) await self.updated_usuario.cloneFrom(item.updated_usuario)

    if (item.deleted) self.deleted = item.deleted
    if (item.inserted) self.inserted = item.inserted
    if (item.updated) self.updated = item.updated
  }
}

export default ColetaItem
