import { CircleExplosionAnimation } from "./CircleExplosionAnimation"
import { CthulhulhulhuAnimation } from "./CthulhulhulhuAnimation"
import { ExplosionAnimation } from "./ExplosionAnimation"
import { FireExplosionAnimation } from "./FireExplosionAnimation"
import { GroundBurstAnimation } from "./GroundBurstAnimation"
import { ProjectileAnimation } from "./ProjectileAnimation"
import { UnnamedOneAnimation } from "./UnnamedOneAnimation"

export class HandleAnimation {
  constructor (target, effect, data, source) {
    this.target = target
    this.effect = effect
    this.data = data
    this.source = source
    this.callback = null
    // this.finallyCallback = null
  }

  async resolve (callback, finallyCallback = null) {
    if (! this.data.animation) {
      await callback()
      // if (finallyCallback) await finallyCallback()
      return
    }

    this.callback = callback
    // this.finallyCallback = finallyCallback

    const animationMapper = {
      circle_explosion: CircleExplosionAnimation,
      fire_explosion: FireExplosionAnimation,
      cthulhulhulhu: CthulhulhulhuAnimation,
      ground_burst: GroundBurstAnimation,
      projectile: ProjectileAnimation,
    }

    if (this[this.data.animation] === undefined && animationMapper[this.data.animation] === undefined) {
      window.errorToast(`No animation ${this.data.animation}`)
      return await callback()
    }

    if (animationMapper[this.data.animation]) {
      return await new animationMapper[this.data.animation]({
        target: this.target,
        effect: this.effect,
        data: this.data,
        source: this.source,
      }).resolve(
        async () => (callback ? await this.callback() : null),
        // async () => (finallyCallback ? await this.finallyCallback() : null),
      )
    }

    return await this[this.data.animation]()
  }

  async energy_pulse () {
    await (this.target || []).forEach(async (target, key) => {
      await new ExplosionAnimation({
        target: target.$el(),
        width: 400,
        color: this.data.color || 'yellow',
      }).resolve(() => {
        if (key === 0) this.callback()
      })
    })
  }

  async unnamed_one () {
    this.callback()
    await new UnnamedOneAnimation({ target: this.source }).resolve()
  }
}
