const enumsBackupNivel = [
  { value: '9', desc: 'Offline', memo: 'Ignorado (Cliente Offline)', color: 'orange', icon: 'notifications_paused' },
  { value: '0', desc: 'Ignorado', memo: 'Ignorado (DB compartilhado)', color: 'grey', icon: 'notifications_paused' },
  { value: '8', desc: 'Erro', memo: 'Ignorado (Problemas Técnicos)', color: 'red', icon: 'notifications_paused' },
  { value: '1', desc: 'Gratuito', memo: 'Nível Gratuito', color: 'green', icon: 'free_breakfast' },
  { value: '2', desc: ' Pago', memo: 'Nível Pago', color: 'blue', icon: 'paid' }
]

export class BackupNivel {
  constructor (pValue) {
    this._value = null
    if (typeof pValue !== 'undefined') this.value = pValue.toString()
  }

  set value (pValue) {
    if (!pValue) return
    var v = pValue.toString()
    this._value = null
    for (let index = 0; index < enumsBackupNivel.length; index++) {
      const element = enumsBackupNivel[index]
      if (element.value === v) {
        this._value = element
      }
    }
  }

  get options () {
    return enumsBackupNivel
  }

  get value () {
    if (this._value) {
      return this._value.value
    } else {
      return null
    }
  }

  get icon () {
    if (this._value) {
      return this._value.icon
    } else {
      return null
    }
  }

  get color () {
    if (this._value) {
      return this._value.color
    } else {
      return null
    }
  }

  toString () {
    if (this._value) {
      return this._value.value
    } else {
      return null
    }
  }

  get description () {
    if (this._value) {
      return this._value.desc
    } else {
      return 'Indefinido'
    }
  }
  get memo () {
    if (this._value) {
      return this._value.memo
    } else {
      return 'Indefinido'
    }
  }
}
