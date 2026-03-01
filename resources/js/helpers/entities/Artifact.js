import { ShakeAnimation } from "./animations/ShakeAnimation"

export class Artifact {
  constructor (card) {
    this.charges = 0
    this.dead = false

    Object.entries(card).forEach(([key, value]) => {
      this[key] = value
    })
  }

  $el () {
    return document.getElementById('game-artifact-' + this.uuid)
      || document.getElementById('artifact-' + this.uuid)
  }

  coords () {
    return {
      x: this.$el().offsetLeft + (this.$el().offsetWidth / 2),
      y: this.$el().offsetTop + (this.$el().offsetHeight / 2),
    }
  }

  async gain_charge (data, source) {
    this.charges += window.game.getAmount(data, source)
    this.charges = Math.max(this.charges, 0)

    await timeout(500)

    window.nextJob()
  }

  async deal_damage (data, source, amount = null) {
    amount = amount || window.game.getAmount(data, source)

    if (this.keywords.includes('delicate') ) {
      amount = amount * 2
    }

    if (this.keywords.includes('life_linked') ) {
      window.game.playerById(this.owner).deal_damage(data, source, amount)
    }

    game.checkTriggers('survive_damage', [this], source)
    game.checkTriggers('took_damage', [this], source)

    await new ShakeAnimation({ target: this, intensity: amount }).resolve(() => {
      window.nextJob()
    })
  }
}
