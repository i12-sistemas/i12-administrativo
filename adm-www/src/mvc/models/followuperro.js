import Vue from 'vue'
import Usuario from 'src/mvc/models/usuario.js'
import DialogAddOrEdit from 'src/pages/cadastro/followup/erros/editdialog.vue'
class FollowupErro {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null
    this.descricao = ''
    this.descricao_old = ''
    this.ativo = true
    this.tipo = 'agenda' // enum('agenda','coleta','dtpromessa')
    this.created_at = null
    this.updated_at = null
    this.created_usuario = new Usuario()
    this.updated_usuario = new Usuario()
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.id !== 'undefined') self.id = item.id
    if (typeof item.descricao !== 'undefined') self.descricao = item.descricao
    self.descricao_old = item.descricao
    if (typeof item.tipo !== 'undefined') self.tipo = item.tipo
    self.ativo = Vue.prototype.$helpers.toBool(item.ativo)

    if (item.created_at) self.created_at = item.created_at
    if (item.updated_at) self.updated_at = item.updated_at
    if (item.created_usuario) await self.created_usuario.cloneFrom(item.created_usuario)
    if (item.updated_usuario) await self.updated_usuario.cloneFrom(item.updated_usuario)
  }

  async find (pID) {
    var self = this
    self.limpardados()
    let ret = await Vue.prototype.$axios.get('v1/errosfollowup/' + pID).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          self.cloneFrom(data.data)
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async save () {
    var self = this
    try {
      // var permite = Vue.prototype.$helpers.permite('cadastros.motoristas.save')
      // if (!permite.ok) throw new Error(permite.msg)

      if (!self.descricao) throw new Error('Descrição é obrigatória')
      if (self.descricao.length < 2) throw new Error('Descrição deve ter no mínimo 2 caracteres')
      if (!self.tipo) throw new Error('Informe o tipo de cadastro')
    } catch (error) {
      return { ok: false, msg: error.message, warning: true }
    }
    let params = {
      id: self.id ? (self.id > 0 ? self.id : null) : null,
      descricao: self.descricao,
      tipo: self.tipo,
      ativo: self.ativo
    }
    let ret = await Vue.prototype.$axios.post('v1/errosfollowup', params).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          if (data.data) self.cloneFrom(data.data)
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async deleteWithQuestion (app) {
    var self = this
    try {
      // var permite = Vue.prototype.$helpers.permite('cadastros.motoristas.delete')
      // if (!permite.ok) throw new Error(permite.msg)
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }

    return new Promise((resolve) => {
      app.$q.dialog({
        title: 'Excluir cadastro?',
        message: 'Para excluir o cadastro "' + self.descricao + '" digite o código ' + self.id + '?',
        prompt: {
          model: '',
          type: 'text' // optional
        },
        cancel: true
      }).onOk(async data => {
        if (parseInt(data) === parseInt(self.id)) {
          var ret = await self.delete()
          resolve(ret)
        } else {
          resolve({ ok: false, msg: 'Informação inválida', warning: true })
        }
      }).onCancel(() => {
        resolve({ ok: false })
      })
    })
  }

  async delete () {
    var self = this

    let ret = await Vue.prototype.$axios.delete('v1/errosfollowup/' + self.id).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async dialogAddOrEdit (app) {
    var self = this
    try {
      // var permite = null
      // if (self.id > 0) {
      //   permite = Vue.prototype.$helpers.permite('cadastros.motoristas.consulta')
      // } else {
      //   permite = Vue.prototype.$helpers.permite('cadastros.motoristas.save')
      // }
      // if (!permite.ok) throw new Error(permite.msg)
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }
    return new Promise((resolve) => {
      app.$q.dialog({
        parent: app,
        component: DialogAddOrEdit,
        dataset: self,
        adding: !(self.id > 0),
        cancel: true
      }).onOk(async retOk => {
        var ret = { ok: retOk }
        resolve(ret)
      }).onCancel(() => {
        resolve({ ok: false })
      })
    })
  }
}

export default FollowupErro
