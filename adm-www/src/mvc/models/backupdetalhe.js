import Vue from 'vue'

class BackupDetalhe {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.md5 = null
    this.name = null
    this.filename = null
    this.size = 0
    this.lastmodified = null
    this.lastcheck = null
    this.bucket = 0
    this.downloadcount = 0
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.md5 !== 'undefined') self.md5 = item.md5
    if (typeof item.filename !== 'undefined') {
      self.filename = item.filename
      var s = self.filename.split('/')
      if (s ? s.length > 0 : false) self.name = s[s.length - 1]
    }
    if (typeof item.size !== 'undefined') self.size = item.size
    if (typeof item.lastmodified !== 'undefined') self.lastmodified = item.lastmodified
    if (typeof item.lastcheck !== 'undefined') self.lastcheck = item.lastcheck
    if (typeof item.bucket !== 'undefined') self.bucket = item.bucket
    if (typeof item.downloadcount !== 'undefined') self.downloadcount = item.downloadcount
  }

  async download (justificativa) {
    var self = this
    let params = {
      motivo: justificativa
    }
    let ret = await Vue.prototype.$axios.get('v1/admin/backup/download/' + self.md5, { params: params }).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.ok = data.ok
        ret.msg = data.msg ? data.msg : ''
        ret.url = data.data
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }
}

export default BackupDetalhe
