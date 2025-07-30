import { ActivatedAnimation } from "./animations/ActivatedAnimation"

export class CardHand {
  constructor (card) {
    Object.entries(card).forEach(([key, value]) => {
      this[key] = value
    })
  }

  $el () {
    return document.getElementById('hand-' + this.uuid)
  }

  coords () {
    if (! this.$el()) {
      return { x: 0, y: 0 }
    }

    return {
      x: this.$el().offsetLeft + (this.$el().offsetWidth / 2),
      y: this.$el().offsetTop + (this.$el().offsetHeight / 2),
    }
  }

  async reduce_cost (data, source) {
    const amount = window.game.getAmount(data)
    this.cost = Math.max(this.cost - amount, 0)

    new ActivatedAnimation({ target: source }).resolve()

    window.nextJob()
  }

  async buff_dude (data, source) {
    if (this.type !== 'ruse') {
      this.power += window.game.getAmount(data, source)
      new ActivatedAnimation({ target: source }).resolve()
    }

    window.nextJob()
  }
  
  async add_maximum_power (data) {
    this.original.power += window.game.getAmount(data, this)
    this.power = this.original.power

    window.nextJob()
  }
}
