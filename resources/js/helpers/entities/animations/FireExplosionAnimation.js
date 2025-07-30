import { Animation } from "./Animation"

export class FireExplosionAnimation extends Animation {
  constructor (...args) {
    super('fire_explosion', ...args)

    this.grace = 900
    this.duration = 100
  }

  async resolve (callback, finallyCallback) {
    const div = this.data.target[0].$el()

    if (div) {
      const width = this.data.width || 400
      const image = 'fire-explosion'

      this._meta = {
        x: (div.offsetLeft + div.offsetWidth / 2) - (width / 2),
        y: (div.offsetTop + div.offsetHeight / 2) - (width / 2),
        width: width,
        image: image,
      }

      super.resolve()
    }

    await window.timeout(this.duration)
    if (callback) callback()

    await window.timeout(this.grace)
    if (finallyCallback) finallyCallback()
  }
}
