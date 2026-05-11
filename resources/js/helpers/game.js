import { SwapTurn } from './events/SwapTurn'
import { PlayDude } from './events/PlayDude'
import { Mulliganed } from './events/Mulliganed'

import { Boss } from './entities/bosses/Boss'
import { Player } from './entities/Player'
import { Artifact } from './entities/Artifact'
import { Attack } from './events/Attack'
import { HandleAnimation } from './entities/animations/HandleAnimation'

import { reactive } from 'vue'
import { Surrender } from './events/Surrender'
import { ExpGain } from './events/ExpGain'
import { PlayRuse } from './events/PlayRuse'
import { Emote } from './events/Emote'
import { Dude } from './entities/Dude'
import { ElDoradoDefensesBoss } from './entities/bosses/ElDoradoDefensesBoss'
import { TwoFacedDevourerBoss } from './entities/bosses/TwoFacedDevourerBoss'

export class Game {
  constructor (_vue) {
    this._vue = _vue
    this.gameId = _vue.gameId
    this.isBossFight = _vue.isBossFight || false
    this.pusher = window.pusher.subscribe(this.gameId)
    this.eventFired = false

    this.completed = _vue.gameState.completed || false
    this.afterGameUpgrades = reactive(_vue.gameState.after_game_upgrades || [])

    this.player = reactive(new Player(_vue.gameState.player))

    if (this.isBossFight) {
      switch (_vue.gameState.opponent.name) {
        case 'El Dorado Defenses': this.opponent = reactive(new ElDoradoDefensesBoss(_vue.gameState.opponent)); break
        case 'Two-Faced Devourer': this.opponent = reactive(new TwoFacedDevourerBoss(_vue.gameState.opponent)); break
        default: this.opponent = reactive(new Boss(_vue.gameState.opponent))
      }

      this.opponent.mulliganed = 1
    } else {
      this.opponent = reactive(new Player(_vue.gameState.opponent))
    }

    this.artifact = _vue.gameState.artifact ? reactive(new Artifact(_vue.gameState.artifact)) : null

    this.currentPlayer = (_vue.gameState.current_player === this.player.id ? this.player : this.opponent)
    this.currentOpponent = (_vue.gameState.current_player === this.player.id ? this.opponent : this.player)

    // Triggers
    this.pusher.bind('event', (data) => {
      window._target = data.data._target || null

      switch (data.event) {
        case 'mulliganed': (new Mulliganed).resolve(this, data); break
        case 'end_turn': (new SwapTurn).resolve(this, data); break
        case 'play_dude': (new PlayDude).resolve(this, data); break
        case 'play_ruse': (new PlayRuse).resolve(this, data); break
        case 'attack': (new Attack).resolve(this, data); break
        case 'surrender': (new Surrender).resolve(this, data); break
        case 'exp_gain': (new ExpGain).resolve(this, data); break
        case 'emote': (new Emote).resolve(this, data); break
        default: window.errorToast(`No trigger for ${data.event}`); break
      }

      if (data.event === 'play_ruse' && this.currentPlayer.doubledRuses > 0) {
        this.currentPlayer.doubledRuses--

        if (! [15, 1271].includes(data.data.card.id)) {
          (new PlayRuse).resolve(this, data, true)
        }
      }

      this.eventFired = false
    })
  }

  haveMulliganed () {
    return (this.player.mulliganed !== -1 && this.opponent.mulliganed !== -1)
  }

  // Post an event to the server
  event (event, data = {}) {
    if (this.eventFired) return

    data._target = this._vue.getTarget()?.uuid || null

    window.axios.post(`/api/games/${this.gameId}/event`, { event: event, data: data })
    this.eventFired = true
  }

  getTargets (type, owner, target = null, data = []) {
    let player, opponent

    if (owner) {
      player = (owner.owner === this.currentPlayer.id ? this.currentPlayer : this.currentOpponent)
      opponent = (owner.owner === this.currentPlayer.id ? this.currentOpponent : this.currentPlayer)
    } else {
      [player, opponent] = [this.currentPlayer, this.currentOpponent]
    }

    type = type || 'player'

    let targets = []

    switch (type) {
      case 'player': targets = [player]; break
      case 'opponent': targets = [opponent]; break
      case 'target_dude': targets = [target]; break
      case 'target_anything': targets = [target]; break
      case 'target_hand': targets = [target]; break
      case 'target_player': targets = [target]; break
      case 'all': targets = [...player.board, ...opponent.board, player, opponent]; break
      case 'all_dudes': targets = [...player.board, ...opponent.board]; break
      case 'all_hand_deck': targets = [...player.hand, ...player.deck]; break
      case 'all_players': targets = [player, opponent]; break
      case 'all_hand': targets = player.hand; break
      case 'source': targets = [target]; break
      case 'attacker': targets = [target]; break
      case 'itself': targets = [owner]; break
      case 'from_uuid': targets = [...player.board, ...opponent.board, player, opponent, ...player.hand].filter((c) => c.uuid === target); break
      case 'opponent_left_most_dude': targets = opponent.board.slice(0, 1); break
      case 'opponent_right_most_dude': targets = opponent.board.slice(-1); break
      case 'player_left_most_dude': targets = player.board.slice(0, 1); break
      case 'player_right_most_dude': targets = player.board.slice(-1); break
      case 'player_left_most_hand': targets = player.hand.slice(0, 1); break
      case 'player_right_most_hand': targets = player.hand.slice(-1); break
      default: window.errorToast(`No target type ${type}`);
    }

    // Apply conditionals
    (data.conditions || []).forEach((condition) => {
      targets = targets.filter((target) => {
        switch (condition.condition) {
          case 'not_self': return target.uuid !== owner.uuid
          case 'tribe': return target.tribes && target.tribes.includes(condition.tribe)
          case 'not_tribe': return ! target.tribes || ! target.tribes.includes(condition.tribe)
          case 'specific_card': return target.id == condition.card
          case 'has_keyword': return target.keywords.includes(condition.keyword)
          case 'owner': return target.owner === ((condition.owner === 'player') ? player.id : opponent.id)
          case 'is_damaged': return target.power < target.original.power
          case 'is_unchanged': return target.power === target.original.power
          case 'is_buffed': return target.power > target.original.power
          case 'was_damaged_this_turn': return target.wasDamagedThisTurn
        }

        return true
      })
    })

    return targets.filter((target) => target !== null)
  }

  getAmount (data, source) {
    if (data.amount !== 'X') {
      return parseInt(data.amount)
    }

    let player, opponent
    if (source) {
      player = (source.owner === this.currentPlayer.id ? this.currentPlayer : this.currentOpponent)
      opponent = (source.owner === this.currentPlayer.id ? this.currentOpponent : this.currentPlayer)
    } else {
      [player, opponent] = [this.currentPlayer, this.currentOpponent]
    }

    const multiplier = parseInt(data.amount_multiplier) || 1
    let amount = 0

    const playerCount = player.board.filter((c) => ! c.dead && c instanceof Dude).length
    const opponentCount = opponent.board.filter((c) => ! c.dead && c instanceof Dude).length

    switch (data.amount_special) {
      case 'for_each_dude_player': amount = playerCount * multiplier; break
      case 'for_each_dude_player_except_self': amount = (playerCount - 1) * multiplier; break
      case 'for_each_dude_opponent': amount = opponentCount * multiplier; break
      case 'for_each_dude': amount = (playerCount + opponentCount) * multiplier; break
      case 'for_each_energy_player': amount = player.energy * multiplier; break
      case 'for_each_energy_opponent': amount = opponent.energy * multiplier; break
      case 'for_each_y_power': amount = Math.floor(source.power / data.amount_y) * multiplier; break
      case 'for_each_card_in_hand': amount = player.hand.length * multiplier; break
      case 'for_each_card_in_opponent_hand': amount = opponent.hand.length * multiplier; break
      case 'for_each_artifact_charge': amount = (source.charges || 0) * multiplier; break
      default: window.errorToast(`No amount special type ${data.amount_special}`);
    }

    return amount
  }

  // Check for any triggers after an effect or event has fired
  // This only pushes to the queue, it doesn't actually run the effect yet
  async checkTriggers (trigger, cardsToCheck, causer = null) {
    window.game._vue.queue(() => {
      let targets

      cardsToCheck
        .filter((card) => card)
        .forEach((dude) => {
          (dude.effects || []).filter((e) => e.trigger === trigger).reverse().forEach(async (effect) => {
            targets = (window.requiresTarget.includes(effect.target))
              ? this._vue.getTarget() || causer // Only use targeted targets if it uses them
              : this.getTargets(effect.target, dude, causer, effect)

            if (! (targets instanceof Array)) {
              targets = [targets]
            }

            this.effect(effect, targets, dude)
          })
        })

      window.nextJob()
    })
  }

  // Do something
  effect (effect, targets, source = null) {
    // Make sure to include the .nextJob() in the event itself
    window.game._vue.queue(async () => {
      let data = effect
      effect = effect.effect

      targets = targets.filter((t) => ! [null, undefined].includes(t))

      // Refetch the targets (see Clyde + Mirror)
      if (data.target && window.shouldRefetchTargets.includes(data.target)) {
        targets = this.getTargets(data.target, source, false, data)
      }

      if (! window.game.areCurrentPlayer() && window._target && targets.length === 0) {
        targets.push(...this.getTargets('from_uuid', null, window._target))
      }

      // Do conditional checks
      targets = targets.filter((target) => {
        if ([null, undefined].includes(target)) return false

        if (! data.trigger_conditions) return true

        return data.trigger_conditions.every((condition) => {
          switch (condition.condition) {
            case 'tribe': return target.tribes.includes(condition.tribe)
            case 'not_tribe': return !target.tribes.includes(condition.tribe)
            case 'not_self': return target.uuid !== source.uuid
            case 'owner': return target.owner === source.owner
            case 'specific_card': return target.id == condition.card
            case 'has_keyword': return target.keywords.includes(condition.keyword)
            case 'is_damaged': return target.power < target.original.power
            case 'is_unchanged': return target.power === target.original.power
            case 'is_buffed': return target.power > target.original.power
            case 'was_damaged_this_turn': return target.wasDamagedThisTurn
          }
        })
      })

      if (targets.length === 0) {
        return window.nextJob()
      }

      await new HandleAnimation(targets, effect, data, source).resolve(() => {
        targets.forEach(async (target) => {
          if (target[effect] === undefined) {
            window.nextJob();
            // window.errorToast(`No effect ${effect} on ${target.uuid}`)
            return
          }

          await target[effect](data, source)
        })
      })
    })
  }

  cleanup () {
    // if (! window.jobs.checkQueueEmpty()) return

    this._vue.$refs.targeting.stopTargeting()

    let playerList = this.currentPlayer.causalityList()
    playerList.forEach((death) => {
      if (death.type === 'dude') {
        this.checkTriggers('dude_dies', [this.artifact, ...this.currentPlayer.board, ...this.currentOpponent.board], [death])
        this.checkTriggers('player_dude_dies', this.currentPlayer.board, [death])
        this.checkTriggers('opponent_dude_dies', this.currentOpponent.board, [death])
      }

      this.checkTriggers('leave_field', [death])
    })

    let opponentList = this.currentOpponent.causalityList()
    opponentList.forEach((death) => {
      if (death.type === 'dude') {
        this.checkTriggers('dude_dies', [this.artifact, ...this.currentPlayer.board, ...this.currentOpponent.board], [death])
        this.checkTriggers('player_dude_dies', this.currentOpponent.board, [death])
        this.checkTriggers('opponent_dude_dies', this.currentPlayer.board, [death])
      }

      this.checkTriggers('leave_field', [death], [death])
    })

    this.updateGameState()

    if (playerList.length <= 0 && opponentList.length <= 0) {
      this.checkGameOver()

      return
    }

    this._vue.queue(async () => {
      await timeout(200)

      this.player.cleanupDead()
      this.opponent.cleanupDead()

      this.checkGameOver()

      window.nextJob()
    })
  }

  isHealingReversed () {
    return [this.artifact, ...this.player.board, ...this.opponent.board]
      .filter((dude) => dude && dude.effects)
      .some((dude) => dude.effects.some((effect) => effect.trigger === 'healing_reversed'))
  }

  checkGameOver () {
    if (this.player.power > 0 && this.opponent.power > 0) return

    this._vue.queue(() => {
      this.completed = true
      this._vue.gameCompleted = true

      let winner = (this.player.power > 0 ? this.player.id : this.opponent.id)
      if (this.player.power <= 0 && this.opponent.power <= 0) {
        winner = 0
      }

      window.axios.put(`/api/games/${this.gameId}/finish/${winner}`, {
        players: [
          { id: this.player.id, power: this.player.power },
          { id: this.opponent.id, power: this.opponent.power },
        ],
      })

      this.updateGameState()
    }, 'end')
  }

  // Play a card from your hand
  playCard (key, data = {}) {
    if (! this.areCurrentPlayer ()) return

    let card = this.player.hand[key]
    if (card.cost > this.player.energy) return

    data.card = card
    data.key = key

    if (card.type === 'dude' || card.type === 'token') {
      this.event('play_dude', data)
    } else if (card.type === 'ruse') {
      this.event('play_ruse', data)
    }
  }

  areCurrentPlayer () {
    return this.currentPlayer.id === this._vue.playerId
  }

  playerById (id) {
    return (id === this.player.id ? this.player : this.opponent)
  }

  // Send data to the server to update the game state
  updateGameState () {
    window.setTimeout(() => {
      let gameState = {}
      gameState['completed'] = this.completed
      gameState['after_game_upgrades'] = this.afterGameUpgrades
      gameState['current_player'] = this.currentPlayer.id
      gameState[`player_${this.player.id}`] = this.player
      gameState[`player_${this.opponent.id}`] = this.opponent

      window.axios.put(`/api/games/${this._vue.gameId}`, {
        gameState: gameState,
      })
    }, 100)
  }
}
