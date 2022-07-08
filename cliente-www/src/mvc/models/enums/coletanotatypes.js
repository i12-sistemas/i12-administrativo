export const enumsEntregaStatus = [
  {
    value: 'col1',
    group: 'Coleta',
    desc: 'Coletas em trânsito p/ o C.D',
    title: 'Em trânsito<br>centro distrib.',
    tooltip: 'Coletados e em trânsito para o centro de distribuição',
    icon: 'filter_tilt_shift',
    color: 'teal-14',
    textcolor: 'white'
  },
  {
    value: 'col2',
    group: 'Coleta',
    desc: 'Coletas pendente nota',
    title: 'Pendente<br>documentação',
    tooltip: 'Coletas com documentação (NF-e) pendente',
    icon: 'error_outline',
    color: 'blue-grey-6',
    textcolor: 'white'
  },
  {
    value: 'ent1',
    group: 'Preparo',
    desc: 'Entregas em separação no C.D.',
    title: 'Em separação<br>centro distrib.',
    tooltip: 'Em separação no centro de distribuição para entrega posterior',
    icon: 'grid_view',
    color: 'brown-7',
    textcolor: 'white'
  },
  {
    value: 'ent2',
    group: 'Em entrega',
    desc: 'Saiu p/ entrega',
    title: 'Saiu para<br>entrega',
    tooltip: 'Saiu para entrega',
    icon: 'local_shipping',
    color: 'amber-9',
    textcolor: 'white'
  },
  {
    value: 'ent3',
    desc: 'Entregue recentemente',
    group: 'Finalizado',
    title: 'Entregue<br>recentemente',
    tooltip: 'Entregas concluidas nos últimos 10 dias',
    icon: 'check_circle_outline',
    color: 'green-6',
    textcolor: 'white'
  },
  {
    value: 'ignored',
    group: 'Arquivado',
    desc: 'Entrega ignorada manualmente',
    title: 'Entrega<br>ignorada',
    tooltip: 'Entrega ignorado por algum processo manual',
    icon: 'snooze',
    color: 'red-10',
    textcolor: 'white'
  }
]

export class EntregaStatus {
  constructor (pValue) {
    this._value = null
    if (pValue) this.value = pValue
  }

  set value (pValue) {
    if (!pValue) return
    var v = pValue.toString()
    this._value = null
    for (let index = 0; index < enumsEntregaStatus.length; index++) {
      const element = enumsEntregaStatus[index]
      if (element.value === v) {
        this._value = element
      }
    }
  }

  get options () {
    return enumsEntregaStatus
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

  get group () {
    if (this._value) {
      return this._value.group
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
}
