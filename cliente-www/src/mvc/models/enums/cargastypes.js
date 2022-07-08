const enumsCargaEntradaTipo = [
  { value: '1', desc: 'Motorista', color: 'green', icon: 'local_shipping' },
  { value: '2', desc: 'Cliente', color: 'cyan', icon: 'accessibility' }
]

const enumsCargaEntradaStatus = [
  { value: '1', desc: 'Em edição', color: 'orange', icon: 'folder_open' },
  { value: '2', desc: 'Encerrado', color: 'blue', icon: 'check' }
]
const enumsCargaEntradaItemProcessamento = [
  { value: '1', desc: 'Auto', color: 'blue', icon: 'auto_fix_high' },
  { value: '2', desc: 'Manual', color: 'deep-orange', icon: 'pan_tool' }
]
const enumsEtiquetaStatus = [
  { value: '1', desc: 'Depósito', color: 'grey-9', icon: 'bookmark' },
  { value: '2', desc: 'Em Tranferência', color: 'grey-9', icon: 'bookmark' },
  { value: '3', desc: 'Em Entrega', color: 'grey-9', icon: 'bookmark' },
  { value: '4', desc: 'Entregue', color: 'grey-9', icon: 'bookmark' },
  { value: '5', desc: 'Extraviado', color: 'grey-9', icon: 'bookmark' }
]
const enumsCargaTransferStatus = [
  { value: '1', desc: 'Em edição', memo: 'Em edição permite alteração', color: 'blue', icon: 'edit' },
  { value: '2', desc: 'Liberado', memo: 'Bloqueia edição e libera carga para transporte', color: 'orange', icon: 'published_with_changes' },
  { value: '3', desc: 'Trânsito', memo: 'Carga em processo de transferência', color: 'red', icon: 'local_shipping' },
  { value: '4', desc: 'Encerrado', memo: 'Processo encerrado', color: 'green', icon: 'check_circle' }
]

const enumsCargaEntregaStatus = [
  { value: '1', desc: 'Em edição', memo: 'Em edição permite alteração', color: 'blue', icon: 'edit' },
  { value: '11', desc: 'Faturamento', memo: 'Aguardando manifesto de frete', color: 'blue-10', icon: 'bookmark' },
  { value: '2', desc: 'Liberado', memo: 'Bloqueia edição e libera carga para transporte', color: 'orange', icon: 'published_with_changes' },
  { value: '3', desc: 'Trânsito', memo: 'Carga em processo de entrega', color: 'red', icon: 'local_shipping' },
  { value: '4', desc: 'Entregue', memo: 'Processo encerrado', color: 'green', icon: 'check_circle' },
  { value: '41', desc: 'Entregue parcial', memo: 'Entrega com recusa de alguns itens', color: 'orange', icon: 'tonality' },
  { value: '5', desc: 'Recusado', memo: 'Entrega recusada pelo destinatário', color: 'red', icon: 'cancel' }
]

// 1=Interno, 2= App do Motorista
const enumsCargaEntregaBaixaOrigem = [
  { value: '1', desc: 'Interno', memo: 'Baixa feita internamente', color: 'edit_note', icon: 'photo_camera' },
  { value: '2', desc: 'Motorista', memo: 'Baixa feita pelo motorista no aplicado', color: 'blue-10', icon: 'local_shipping' }
]

// A=Automatico Camera QRCode, F=File (Arquivo importado), M=Manual
const enumsCargaEntregaBaixaOperacao = [
  { value: 'A', desc: 'Câmera', memo: 'Captura da foto do comprovante pela câmera do celular', color: 'blue', icon: 'photo_camera' },
  { value: 'F', desc: 'Arquivo', memo: 'Comprovante importado através de um arquivo de imagem', color: 'blue-10', icon: 'file_upload' },
  { value: 'M', desc: 'Digitação', memo: 'Digitação manual da carga', color: 'orange', icon: 'edit_note' }
]

// 1=Ok processado, 2=Pendente processamento, 3 Processado com erro\n
const enumsCargaEntregaMDFeStatus = [
  { value: '1', desc: 'Processado', memo: 'Leitura do XML processada com sucesso', color: 'positive', icon: 'check' },
  { value: '2', desc: 'Pendente', memo: 'Pendente leitura do XML', color: 'orange', icon: 'published_with_changes' },
  { value: '21', desc: 'Download', memo: 'Fazendo download dos CT-e', color: 'cyan', icon: 'get_app' },
  { value: '22', desc: 'Leitura', memo: 'Fazendo leitura dos CT-e', color: 'teal', icon: 'format_list_numbered' },
  { value: '3', desc: 'Erro', memo: 'Erros encontrados no processamento', color: 'red', icon: 'error' }
]

// 1=Ok processado, \n2=Pendente download\n3=Pendente processar XML\n9=Arquivado por erro e excesso de tentativa\n
const enumsCargaEntregaCTeStatus = [
  { value: '1', desc: 'Processado', memo: 'Leitura do XML processada com sucesso', color: 'positive', icon: 'check' },
  { value: '2', desc: 'Download', memo: 'Pendente download do  XML', color: 'orange', icon: 'published_with_changes' },
  { value: '3', desc: 'Leitura', memo: 'Pendente leitura do  XML', color: 'blue', icon: 'format_list_numbered' },
  { value: '9', desc: 'Arquivado', memo: 'Arquivado por erro e excesso de tentativa', color: 'red', icon: 'error' }
]

const enumsEtiquetaLogAction = [
  { value: 'add', desc: 'Inclusão', memo: 'Inclusão do item na carga', color: 'blue', icon: 'add' },
  { value: 'delete', desc: 'Exclusão', memo: 'Exclusão do item da carga', color: 'red', icon: 'clear' },
  { value: 'update', desc: 'Alteração', memo: 'Alteração do carga associada', color: 'green', icon: 'edit' },
  { value: 'print', desc: 'PDF', memo: 'Gerou PDF da etiqueta', color: 'black', icon: 'picture_as_pdf' }
]

const enumsEtiquetaLogOrigem = [
  { value: 'cargaentradaitem', desc: 'Entrada', memo: 'Carga de entrega', color: 'purple', icon: 'double_arrow' },
  { value: 'cargatransferitem', desc: 'Transferência', memo: 'Carga de transferência', color: 'green', icon: 'swap_horiz' },
  { value: 'cargaentregaitem', desc: 'Entrega', memo: 'Carga de entrega', color: 'blue', icon: 'local_shipping' },
  { value: 'paleteitem', desc: 'Palete', memo: 'Palete de volumes', color: 'indigo', icon: 'widgets' },
  { value: 'etiqueta', desc: 'Etiqueta', memo: 'Etiqueta', color: 'black', icon: 'bookmark' }
]

const enumsPaletesStatus = [
  { value: '1', desc: 'Em aberto', memo: 'Em edição', color: 'white', bgcolor: 'blue-grey', icon: 'edit' },
  { value: '2', desc: 'Lacrado', memo: 'Lacrado não permite edição', color: 'white', bgcolor: 'blue-10', icon: 'lock' },
  { value: '3', desc: 'Despachado', memo: 'Despachado para outra unidade (processo irreversível)', color: 'white', bgcolor: 'green', icon: 'local_shipping' },
  { value: '4', desc: 'Cancelado', memo: 'Palete desfeito (processo irreversível)', color: 'white', bgcolor: 'red-10', icon: 'clear' }
]

export class CargaEntradaTipo {
  constructor (pValue) {
    this._value = null
    if (pValue) this.value = pValue
  }

  set value (pValue) {
    if (!pValue) return
    var v = pValue.toString()
    this._value = null
    for (let index = 0; index < enumsCargaEntradaTipo.length; index++) {
      const element = enumsCargaEntradaTipo[index]
      if (element.value === v) {
        this._value = element
      }
    }
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
}

export class CargaEntradaStatus {
  constructor (pValue) {
    this._value = null
    if (pValue) this.value = pValue
  }

  set value (pValue) {
    if (!pValue) return
    var v = pValue.toString()
    this._value = null
    for (let index = 0; index < enumsCargaEntradaStatus.length; index++) {
      const element = enumsCargaEntradaStatus[index]
      if (element.value === v) {
        this._value = element
      }
    }
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
}

export class CargaEntradaItemProcessamento {
  constructor (pValue) {
    this._value = null
    if (pValue) this.value = pValue
  }

  set value (pValue) {
    if (!pValue) return
    var v = pValue.toString()
    this._value = null
    for (let index = 0; index < enumsCargaEntradaItemProcessamento.length; index++) {
      const element = enumsCargaEntradaItemProcessamento[index]
      if (element.value === v) {
        this._value = element
      }
    }
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
}

export class EtiquetaStatus {
  constructor (pValue) {
    this._value = null
    if (pValue) this.value = pValue
  }

  set value (pValue) {
    if (!pValue) return
    var v = pValue.toString()
    this._value = null
    for (let index = 0; index < enumsEtiquetaStatus.length; index++) {
      const element = enumsEtiquetaStatus[index]
      if (element.value === v) {
        this._value = element
      }
    }
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
}

export class CargaTransferStatus {
  constructor (pValue) {
    this._value = null
    if (pValue) this.value = pValue
  }

  set value (pValue) {
    if (!pValue) return
    var v = pValue.toString()
    this._value = null
    for (let index = 0; index < enumsCargaTransferStatus.length; index++) {
      const element = enumsCargaTransferStatus[index]
      if (element.value === v) {
        this._value = element
      }
    }
  }

  get options () {
    return enumsCargaTransferStatus
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

  get memo () {
    if (this._value) {
      return this._value.memo
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

export class CargaEntregaStatus {
  constructor (pValue) {
    this._value = null
    if (pValue) this.value = pValue
  }

  set value (pValue) {
    if (!pValue) return
    var v = pValue.toString()
    this._value = null
    for (let index = 0; index < enumsCargaEntregaStatus.length; index++) {
      const element = enumsCargaEntregaStatus[index]
      if (element.value === v) {
        this._value = element
      }
    }
  }

  get options () {
    return enumsCargaEntregaStatus
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

  get memo () {
    if (this._value) {
      return this._value.memo
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

export class CargaEntregaMDFeStatus {
  constructor (pValue) {
    this._value = null
    if (pValue) this.value = pValue
  }

  set value (pValue) {
    if (!pValue) return
    var v = pValue.toString()
    this._value = null
    for (let index = 0; index < enumsCargaEntregaMDFeStatus.length; index++) {
      const element = enumsCargaEntregaMDFeStatus[index]
      if (element.value === v) {
        this._value = element
      }
    }
  }

  get options () {
    return enumsCargaEntregaMDFeStatus
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

  get memo () {
    if (this._value) {
      return this._value.memo
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

export class CargaEntregaBaixaOperacao {
  constructor (pValue) {
    this._value = null
    if (pValue) this.value = pValue
  }

  set value (pValue) {
    if (!pValue) return
    var v = pValue.toString()
    this._value = null
    for (let index = 0; index < enumsCargaEntregaBaixaOperacao.length; index++) {
      const element = enumsCargaEntregaBaixaOperacao[index]
      if (element.value === v) {
        this._value = element
      }
    }
  }

  get options () {
    return enumsCargaEntregaBaixaOperacao
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

  get memo () {
    if (this._value) {
      return this._value.memo
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

export class CargaEntregaBaixaOrigem {
  constructor (pValue) {
    this._value = null
    if (pValue) this.value = pValue
  }

  set value (pValue) {
    if (!pValue) return
    var v = pValue.toString()
    this._value = null
    for (let index = 0; index < enumsCargaEntregaBaixaOrigem.length; index++) {
      const element = enumsCargaEntregaBaixaOrigem[index]
      if (element.value === v) {
        this._value = element
      }
    }
  }

  get options () {
    return enumsCargaEntregaBaixaOrigem
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

  get memo () {
    if (this._value) {
      return this._value.memo
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

export class CargaEntregaCTeStatus {
  constructor (pValue) {
    this._value = null
    if (pValue) this.value = pValue
  }

  set value (pValue) {
    if (!pValue) return
    var v = pValue.toString()
    this._value = null
    for (let index = 0; index < enumsCargaEntregaCTeStatus.length; index++) {
      const element = enumsCargaEntregaCTeStatus[index]
      if (element.value === v) {
        this._value = element
      }
    }
  }

  get options () {
    return enumsCargaEntregaCTeStatus
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

  get memo () {
    if (this._value) {
      return this._value.memo
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

export class EtiquetaLogAction {
  constructor (pValue) {
    this._value = null
    if (pValue) this.value = pValue
  }

  set value (pValue) {
    if (!pValue) return
    var v = pValue.toString()
    this._value = null
    for (let index = 0; index < enumsEtiquetaLogAction.length; index++) {
      const element = enumsEtiquetaLogAction[index]
      if (element.value === v) {
        this._value = element
      }
    }
  }

  get options () {
    return enumsEtiquetaLogAction
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

  get memo () {
    if (this._value) {
      return this._value.memo
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

export class EtiquetaLogOrigem {
  constructor (pValue) {
    this._value = null
    if (pValue) this.value = pValue
  }

  set value (pValue) {
    if (!pValue) return
    var v = pValue.toString()
    this._value = null
    for (let index = 0; index < enumsEtiquetaLogOrigem.length; index++) {
      const element = enumsEtiquetaLogOrigem[index]
      if (element.value === v) {
        this._value = element
      }
    }
  }

  get options () {
    return enumsEtiquetaLogAction
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

  get memo () {
    if (this._value) {
      return this._value.memo
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

export class PaletesStatus {
  constructor (pValue) {
    this._value = null
    if (pValue) this.value = pValue
  }

  set value (pValue) {
    if (!pValue) return
    var v = pValue.toString()
    this._value = null
    for (let index = 0; index < enumsPaletesStatus.length; index++) {
      const element = enumsPaletesStatus[index]
      if (element.value === v) {
        this._value = element
      }
    }
  }

  get options () {
    return enumsPaletesStatus
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

  get bgcolor () {
    if (this._value) {
      return this._value.bgcolor
    } else {
      return null
    }
  }

  get memo () {
    if (this._value) {
      return this._value.memo
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
