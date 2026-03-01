import { Animation } from "./Animation"

export class GreatDevouringAnimation extends Animation {
  constructor (...args) {
    super('great_devouring', ...args)

    this.grace = 3000
    this.duration = 500
  }

  async resolve (callback = null, finallyCallback = null) {
    const div = window.game.currentOpponent.$boardEl()

    this._meta = {
      jaws: {
        height: div.offsetHeight,
        width: div.offsetWidth,
        top: div.offsetTop,
        left: div.offsetLeft,
      },
    }

    super.resolve()

    await window.timeout(this.duration)

    if (callback) callback()

    await window.timeout(this.grace)

    if (finallyCallback) finallyCallback()

    // Clean up
    // document.getElementById(`jaws-${this.uuid}`).remove()
  }
}
