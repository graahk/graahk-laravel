import { Animation } from "./Animation"

export class EmoteAnimation extends Animation {
  constructor (...args) {
    super('emote', ...args)

    this.grace = 1000
    this.duration = 4000
  }

  async resolve (callback, finallyCallback) {
    const div = this.data.target.$el()

    if (div) {
      const width = div.offsetWidth * 0.8

      this._meta = {
        emote: `emotes/${this.data.emote}`,
        x: (div.offsetLeft + div.offsetWidth / 2) - (width / 2),
        y: (div.offsetTop + div.offsetHeight / 2) - (width / 2),
        width: width,
      }

      super.resolve()
    }

    window.setTimeout(() => document.getElementById(this.uuid).remove(), this.fullDuration())
  }
}
