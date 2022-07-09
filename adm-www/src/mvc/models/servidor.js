import Database from 'src/mvc/models/database.js'
class Servidor {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.version = null
    this.server_uuid = null
    this.created_at = null
    this.updated_at = null
    this.database_count = 0
    this.serialnumber = null
    this.hostname = null
    delete this.databases
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.version !== 'undefined') self.version = item.version
    if (typeof item.server_uuid !== 'undefined') self.server_uuid = item.server_uuid
    if (typeof item.updated_at !== 'undefined') self.updated_at = item.updated_at
    if (typeof item.created_at !== 'undefined') self.created_at = item.created_at
    if (typeof item.database_count !== 'undefined') self.database_count = item.database_count
    if (typeof item.serialnumber !== 'undefined') self.serialnumber = item.serialnumber
    if (typeof item.hostname !== 'undefined') self.hostname = item.hostname
    if (typeof item.databases !== 'undefined') {
      self.databases = []
      for (let index = 0; index < item.databases.length; index++) {
        const element = item.databases[index]
        var database = new Database(element)
        self.databases.push(database)
      }
    }
  }
}

export default Servidor
