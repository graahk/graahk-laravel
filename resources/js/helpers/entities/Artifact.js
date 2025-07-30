export class Artifact {
  constructor (card) {
    this.charges = 0

    Object.entries(card).forEach(([key, value]) => {
      this[key] = value
    })
  }

  $el () {
    return document.getElementById('game-artifact-' + this.uuid)
  }

  coords () {
    return {
      x: this.$el().offsetLeft + (this.$el().offsetWidth / 2),
      y: this.$el().offsetTop + (this.$el().offsetHeight / 2),
    }
  }

  async gain_charge (data, source) {
    this.charges += window.game.getAmount(data, source)

    await timeout(500)

    window.nextJob()
  }
}
