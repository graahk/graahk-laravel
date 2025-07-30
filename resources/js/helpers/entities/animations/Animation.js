export class Animation {
  constructor (animation, data = {}, duration, grace = 1) {
    this.animation = animation
    this.duration = duration

    this.grace = grace
    this.data = data
    this.uuid = Math.floor(Math.random() * 100) + '' + Date.now()
  }

  resolve () {
    window.game._vue.animations.push(this)
  }

  fullDuration () {
    return this.duration + this.grace
  }

  addClass ($el, className) {
    if (! $el) return

    $el.classList.add(className)
    window.setTimeout(
      () => $el.classList.remove(className),
      this.duration + this.grace + 100
    )
  }
}
