const menu = [
  {
    categoria: 'Coletas',
    itens: [
      {
        icon: 'watch_later',
        text: 'Coletas agendadas',
        to: { name: 'coletas.consulta' }
      },
      {
        icon: 'filter_tilt_shift',
        text: 'Coletados e em trânsito para o centro de distribuição',
        to: { name: 'coletas.consulta' }
      },
      {
        icon: 'error_outline',
        text: 'Coletas com documentação (NF-e) pendente',
        to: { name: 'coletas.consulta' }
      }
    ]
  },
  {
    categoria: 'Entregas',
    itens: [
      {
        icon: 'grid_view',
        text: 'Em separação no centro de distribuição',
        to: { name: 'coletas.consulta' }
      },
      {
        icon: 'local_shipping',
        text: 'Saiu para entrega',
        to: { name: 'coletas.consulta' }
      },
      {
        icon: 'check_circle_outline',
        text: 'Entregas concluidas nos últimos 10 dias',
        to: { name: 'coletas.consulta' }
      }
    ]
  },
  {
    categoria: 'Notas',
    itens: [
      {
        icon: 'fas fa-barcode',
        text: 'Notas pendente arquivo'
      }
    ]
  }
]

class MenuLateral {
  constructor () {
    this.menu = null
  }

  async processar (pUsuario) {
    this.menu = menu
  }
}

export default MenuLateral
