import { reactive } from 'vue'
import { Dude } from './Dude'
import { ExplosionAnimation } from './animations/ExplosionAnimation'
import { ShakeAnimation } from './animations/ShakeAnimation'
import { HealAnimation } from './animations/HealAnimation'
import { ActivatedAnimation } from './animations/ActivatedAnimation'
import { Token } from './Token'
import { Artifact } from './Artifact'
import { CardHand } from './CardHand'

export class Player {
  constructor (player) {
    this.id = player.id
    this.name = player.name
    this.avatar = player.avatar
    this.uuid = player.uuid
    this.owner = player.id
    this.power = player.power
    this.originalPower = player.originalPower
    this.energy = player.energy
    this.keywords = player.keywords || []
    this.doubledRuses = player.doubledRuses || 0

    this.hand = reactive(player.hand.map((card) => {
      return reactive(new CardHand(card))
    }))

    this.deck = reactive(player.deck.map((card) => {
      return reactive(new CardHand(card))
    }))

    this.graveyard = reactive(player.graveyard.map((card) => {
      return reactive(new CardHand(card))
    }))

    this.board = reactive(player.board.map((card) => {
      if (card.type === 'artifact') {
        return reactive(new Artifact(card))
      } else if (card.type === 'token') {
        return reactive(new Token(card))
      } else {
        return reactive(new Dude(card))
      }
    }))

    this.fatigue = player.fatigue || 0
    this.drawsThisTurn = player.drawsThisTurn || 0

    // -1 means not mulliganed yet, otherwise it's the amount of cards mulliganed
    this.mulliganed = (player.mulliganed === undefined ? -1 : player.mulliganed)
  }

  $ref () {
    return window.game._vue.$refs['player-' + this.uuid]
  }

  $el () {
    return this.$ref().$el.querySelector('.avatar')
  }

  $boardRef () {
    return window.game._vue.$refs['board-' + this.uuid]
  }

  $boardEl () {
    return this.$boardRef().$el
  }

  coords () {
    return {
      x: this.$el().offsetLeft + (this.$el().offsetWidth / 2),
      y: this.$el().offsetTop + (this.$el().offsetHeight / 2),
    }
  }

  shuffle () {
    this.deck = this.deck.sort(() => Math.random() - 0.5)
  }

  causalityList () {
    let deaths = this.board
      .filter((card) => (card.power <= 0 && ! card.keywords.includes('tireless')) || card.dead)
      .filter((card) => card.type !== 'artifact')
      .filter((card) => ! card.deathChecked)

    deaths.forEach((card) => {
      card.deathChecked = true
    })

    return deaths
  }

  cleanupDead () {
    this.board.filter((card) => card.deathChecked).forEach((card) => {
      card.dead = true

      // VueJS handles the animation
      this.graveyard.push(card)
      this.board.splice(this.board.map((c) => c.uuid).indexOf(card.uuid), 1)
    })
  }

  end_turn () {
    if (window.game.areCurrentPlayer())  {
      window.game.event('end_turn')
    }

    window.nextJob()
  }

  async give_keyword (data, source) {
    if (! this.keywords.includes(data.keyword) && ['scorching'].includes(data.keyword)) {
      this.keywords.push(data.keyword)
    }

    window.nextJob()
  }

  async gain_energy (data, source) {
    if (source) {
      new ActivatedAnimation({ target: source }).resolve()
    }

    await new ExplosionAnimation({ target: this.$ref().$refs.energy }).resolve(
      () => this.energy += window.game.getAmount(data, source),
      () => {
        window.game.checkTriggers('gain_energy', [window.game.artifact, ...this.board], source)
        window.nextJob()
      }
    )
  }

  async spawn (data, source, type = 'token') {
    let card
    const amount = window.game.getAmount(data, source)

    for (let index = 0; index < amount; index++) {
      card = await axios.get(`/api/cards/${data[type]}`)
      card.data.owner = this.id
      card.data.uuid = window.uuid(this.uuid + this.board.length + this.graveyard.length)

      if (source) {
        card.data.level = source.level || 1
        new ActivatedAnimation({ target: source }).resolve()
      }

      if (type === 'token') {
        this.pushToBoard(reactive(new Token(card.data)))
      } else {
        this.pushToBoard(reactive(new Dude(card.data)))
      }
    }
  }

  async spawn_token (data, source) {
    await this.spawn(data, source, 'token')
    window.nextJob()
  }

  async spawn_dude (data, source) {
    await this.spawn(data, source, 'dude')
    window.nextJob()
  }

  async draw_cards (data, source) {
    const amount = window.game.getAmount(data, source)

    for (let index = 0; index < amount; index++) {
      if (this.deck.length <= 0) {
        this.fatigue += 100
        this.power -= this.fatigue
        window.errorToast(`Deck empty! Taking ${this.fatigue} fatigue damage!`)

        new ShakeAnimation({ target: this }).resolve()
        new ExplosionAnimation({
          target: this.$el(),
          width: 500,
          image: 'explosion/red',
        }).resolve()

        await timeout(200)
      } else {
        this.hand.push(this.deck.pop())
        this.drawsThisTurn++
        await timeout(100)
        this.hasDrawnCard(source)
      }
    }

    window.nextJob()
  }

  async draw_specific_tribe (data, source) {
    let uuids = this.deck
      .filter((card) => card.tribes !== null)
      .filter((card) => card.tribes.includes(data.tribe))
      .map((card) => card.uuid)

    await this.drawFromSpecificDeck(data, uuids, source)

    window.nextJob()
  }

  async draw_specific_dude (data, source) {
    let uuids = this.deck
      .filter((card) => card.id == data.dude)
      .map((card) => card.uuid)

    await this.drawFromSpecificDeck(data, uuids, source)

    window.nextJob()
  }

  async draw_specific_cost (data, source) {
    let uuids = this.deck
      .filter((card) => {
        switch (data.operator) {
          case 'greater than equal': return card.cost >= parseInt(data.cost); break
          case 'less than equal': return card.cost <= parseInt(data.cost); break
          case 'equal to': return card.cost === parseInt(data.cost); break
        }
      })
      .map((card) => card.uuid)

    await this.drawFromSpecificDeck(data, uuids, source)

    window.nextJob()
  }

  async drawFromSpecificDeck (data, uuids, source) {
    let key
    const amount = window.game.getAmount(data, source)

    for (let index = 0; index < amount; index++) {
      key = this.deck.indexOf(this.deck.find((card) => card.uuid === uuids[index]))
      if (key === -1) {
        window.errorToast('No more cards to draw')
        new ShakeAnimation({ target: source }).resolve()
        await timeout(500)
      } else {
        this.drawsThisTurn++
        this.hand.push(this.deck.splice(key, 1)[0])
        new ActivatedAnimation({ target: source }).resolve()
        await timeout(1000)
        this.hasDrawnCard(source)
      }
    }
  }

  async hasDrawnCard (source) {
    window.game.checkTriggers('draw_card', [window.game.artifact, ...this.board], source)
    if (this.drawsThisTurn > 1 && window.game.areCurrentPlayer()) {
      window.game.checkTriggers('draw_second_card', [window.game.artifact, ...this.board], source)
    }
  }

  async deal_damage (data, source, amount = null) {
    amount = amount || window.game.getAmount(data, source)
    this.power -= amount

    if (amount > 0) {
      this.wasDamagedThisTurn = true
    }

    if (source) {
      new ActivatedAnimation({ target: source }).resolve()
    }

    await new ShakeAnimation({ target: this, intensity: amount }).resolve(() => {
      window.nextJob()
    })

    window.cleanupTimer = setTimeout(() => {
      window.game.cleanup()
    }, 500)
  }

  async heal (data, source) {
    if (window.game.isHealingReversed()) {
      this.deal_damage(data, source)
      return
    }

    if (this.power < this.originalPower) {
      this.power = Math.min(
        this.power + window.game.getAmount(data, source),
        this.originalPower
      )
    }

    if (source && this.uuid !== source.uuid) {
      new ActivatedAnimation({ target: source }).resolve()
    }

    await new HealAnimation({ target: this }).resolve(null, () => {
      window.nextJob()
    })
  }

  async double_ruse (data, source) {
    this.doubledRuses += Math.max(window.game.getAmount(data, source), 0)

    if (source) {
      new ActivatedAnimation({ target: source }).resolve()
    }

    window.nextJob()
  }

  ready_dudes () {
    this.board.forEach((dude) => {
      if (dude instanceof Dude) {
        dude.ready_dudes()
      }
    })
  }

  stun () {

  }

  pushToBoard (card) {
    this.board.push(card)
  }
}
