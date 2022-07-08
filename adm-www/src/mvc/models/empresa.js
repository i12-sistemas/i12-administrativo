import Vue from 'vue'
class Empresa {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.codempresa = null
    this.apelido = ''
    this.key = null
    this.fotourl = null
    this.cidade = ''
    this.telefone = null
    this.cnpj = null
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (item.codempresa) self.codempresa = item.codempresa
    if (item.apelido) self.apelido = item.apelido
    if (item.key) self.key = item.key
    if (item.fotourl) self.fotourl = item.fotourl
    if (item.cidade) self.cidade = item.cidade
    if (item.telefone) self.telefone = item.telefone
    if (item.cnpj) self.cnpj = item.cnpj
  }

  async findByKey (pKey) {
    var self = this
    self.limpardados()
    try {
      if (!pKey) throw new Error('Key inválido.')
    } catch (error) {
      return { ok: false, msg: error.message }
    }
    var params = {
      key: pKey
    }
    // eslint-disable-next-line no-return-await
    let ret = await Vue.prototype.$axios.post('auth/web/empresa/login/check', params).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          data = data.data
          ret.ok = data.codempresa ? data.codempresa > 0 : false
          self.cloneFrom(data)
        }
      }
      return ret
    }).catch(error => {
      let msg = error
      if (error.response) {
        msg = 'Code: ' + error.response.status + ' - ' + error.response.data.message
      } else {
        msg = error.message
      }
      return { ok: false, msg: 'Falha ao consultar servidor online: ' + msg }
    })
    return ret
  }
}

export default Empresa
