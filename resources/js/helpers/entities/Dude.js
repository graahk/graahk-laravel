import { ActivatedAnimation } from "./animations/ActivatedAnimation"
import { HealAnimation } from "./animations/HealAnimation"
import { ShakeAnimation } from "./animations/ShakeAnimation"
import { reactive } from "vue"
import { Debuff } from "./Debuff"
import { ReadyAnimation } from "./animations/ReadyAnimation"

export class Dude {
  constructor (card) {
    Object.entries(card).forEach(([key, value]) => {
      this[key] = value
    })

    this.dead = this.dead || false
    this.debuffs = this.debuffs || []
  }

  $el () {
    return document.getElementById('dude-' + this.uuid)
      || document.getElementById('token-' + this.uuid)
  }

  coords () {
    return {
      x: this.$el().offsetLeft + (this.$el().offsetWidth / 2),
      y: this.$el().offsetTop + (this.$el().offsetHeight / 2),
    }
  }

  async reset () {
    Object.entries(this.original).forEach(([key, value]) => {
      this[key] = value
    })
  }

  async reset_health () {
    this.power = this.original.power

    await new ActivatedAnimation({ target: this }).resolve(null, () => {
      window.nextJob()
    })
  }

  async reduce_cost (data) {
    const amount = window.game.getAmount(data)
    this.cost = Math.max(this.cost - amount, 0)

    window.nextJob()
  }

  async silence () {
    this.effects = []
    this.keywords = []

    if (! this.debuffs.some((debuff) => debuff.type === 'silenced')) {
      this.debuffs.push(new Debuff({ type: 'silenced', visual: 'silenced' }))
    }

    await new ActivatedAnimation({ target: this }).resolve(null, () => {
      window.nextJob()
    })
  }

  async deal_damage (data, source, amount = null) {
    amount = amount || window.game.getAmount(data, source)
    this.power -= amount

    if (source && data.animation !== 'projectile') {
      new ActivatedAnimation({ target: source }).resolve()
    }

    if (this.power <= 0) {
      this.dead = ! this.keywords.includes('tireless')
      this.power = 0
    }

    if (this.keywords.includes('withering') && amount > 0) {
      this.dead = true
      this.power = 0
    }

    if (! this.dead) {
      game.checkTriggers('survive_damage', [this], source)
    }

    game.checkTriggers('took_damage', [this], source)

    await new ShakeAnimation({ target: this, intensity: amount }).resolve(() => {
      window.nextJob()
    })
  }

  async deal_damage_to_opponent (data, source) {
    const player = window.game.playerById(source.opponent)

    player.deal_damage(data, source, this.power)
  }

  async deal_damage_to_player (data, source) {
    const player = window.game.playerById(source.owner)

    player.deal_damage(data, source, this.power)
  }

  async heal (data, source) {
    if (window.game.isHealingReversed()) {
      this.deal_damage(data, source)
      return
    }

    if (this.power < this.original.power && ! this.dead) {
      this.power = Math.min(
        this.power + window.game.getAmount(data, source),
        this.original.power
      )

      game.checkTriggers('healed', [this], source)
      game.checkTriggers('dude_heals_another', [window.game.artifact, ...window.game.getTargets('all_dudes')], this)

      if (this.power >= this.original.power) {
        game.checkTriggers('dude_fully_healed', [this], source)
      }
    }

    await new HealAnimation({ target: this }).resolve(null, () => {
      window.nextJob()
    })
  }

  async add_maximum_power (data) {
    this.original.power += window.game.getAmount(data, this)
    this.original.power = Math.max(this.original.power, 0)

    await new HealAnimation({ target: this }).resolve(null, () => {
      window.nextJob()
    })
  }

  async kill () {
    this.dead = true
    this.power = 0

    await new ShakeAnimation({ target: this }).resolve(() => {
      window.nextJob()
    })
  }

  async duplicate (data, source, player = null) {
    player = player || window.game.playerById(this.owner)

    let clone = reactive(new Dude(
      JSON.parse(JSON.stringify(this))
    ))

    clone.owner = player.id
    clone.uuid = window.uuid(player.uuid + player.board.length + player.graveyard.length + player.hand.length)
    clone.highlighted = false
    player.pushToBoard(clone)

    // TODO: animation
    await new ShakeAnimation({ target: this }).resolve(() => {
      window.nextJob()
    })
  }

  async duplicate_to_player (data, source) {
    const player = window.game.playerById(data.player === 'player' ? source.owner : source.opponent)

    this.duplicate(data, source, player)
  }

  async ready_dudes () {
    this.willClearStuns = this.debuffs.some((debuff) => debuff.type === 'stun')
    this.ready = ! this.debuffs.some((debuff) => debuff.type === 'stun')
      && ! this.keywords.includes('scenery')

    if (this.ready) {
      await new ReadyAnimation({ target: this }).resolve(null, () => {
        window.nextJob()
      })
    } else {
      window.nextJob()
    }
  }

  async stun (data) {
    this.debuffs.push(new Debuff({
      type: 'stun',
      visual: data.stun_type || 'web',
    }))

    this.ready = false

    await new ActivatedAnimation({ target: this }).resolve(null, () => {
      window.nextJob()
    })
  }

  async buff_dude (data, source) {
    if (! this.dead) {
      this.power += window.game.getAmount(data, source)

      await new ActivatedAnimation({ target: this }).resolve(() => {}, () => {
        window.nextJob()
      })
    } else {
      await timeout(1000)
      window.nextJob()
    }
  }

  bounce () {
    if (this.dead) {
      window.nextJob()
      return
    }

    let player = window.game.playerById(this.owner)

    player.board.splice(player.board.map((c) => c.uuid).indexOf(this.uuid), 1)

    this.reset_health()

    // Turn it into a normal object
    this.highlighted = false
    this.glowing = false
    this.keywords = this.original.keywords
    this.debuffs = []
    player.hand.push(JSON.parse(JSON.stringify(this)))

    window.nextJob()
  }

  async shuffle_into_deck () {
    let player = window.game.playerById(this.owner)

    await new ShakeAnimation({ target: this }).resolve(() => {
      this.reset_health()
      player.deck.push(player.board.splice(player.board.map((c) => c.uuid).indexOf(this.uuid), 1)[0])
      player.shuffle()
      window.nextJob()
    })
  }

  async shuffle_into_opponents_deck () {
    let player = window.game.playerById(this.owner)
    let opponent = window.game.playerById(this.opponent)

    await new ShakeAnimation({ target: this }).resolve(() => {
      this.softReset()
      opponent.deck.push(player.board.splice(player.board.map((c) => c.uuid).indexOf(this.uuid), 1)[0])
      opponent.shuffle()
      window.nextJob()
    })
  }

  async give_keyword (data, source) {
    if (! this.keywords.includes(data.keyword)) {
      this.keywords.push(data.keyword)

      if (data.keyword === 'rush') this.ready = true
      if (data.keyword === 'scenery') this.ready = false
    }

    new ActivatedAnimation({ target: source }).resolve()
    new ActivatedAnimation({ target: this }).resolve()

    window.nextJob()
  }

  async unnamed_one () {
    let player = window.game.playerById(this.owner)
    this.power = parseInt(player.graveyard.length * 50)

    window.nextJob()
  }

  async activate () {
    if (this.dead) return

    window.game.checkTriggers('activate', [this])

    window.nextJob()
  }
}
