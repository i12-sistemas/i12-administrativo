
import DatabaseCliente from 'src/mvc/models/databasecliente.js'
class Database {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.database = null
    this.version_icomdb = null
    delete this.clientes
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.database !== 'undefined') self.database = item.database
    if (typeof item.version_icomdb !== 'undefined') self.version_icomdb = item.version_icomdb
    if (typeof item.clientes !== 'undefined') {
      self.clientes = []
      for (let index = 0; index < item.clientes.length; index++) {
        const element = item.clientes[index]
        var cliente = new DatabaseCliente(element)
        self.clientes.push(cliente)
      }
    }
  }
}

export default Database
