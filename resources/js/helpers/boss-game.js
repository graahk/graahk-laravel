import { Game } from './game'

import { Artifact } from './entities/Artifact'

export class BossGame extends Game {
  constructor (_vue) {
    super(_vue)

    if (! this.opponent.board.length) {
      _vue.gameState.boss_artifacts.forEach((artifact) => {
        artifact = new Artifact(artifact)
        this.opponent.pushToBoard(artifact)
      })
    }
  }

  // Play a card from your hand
  playCard (key, data = {}) {
    if (this.areCurrentPlayer()) {
      super.playCard(key, data)
      return
    }

    let card = this.opponent.hand[key]

    data.card = card
    data.key = key

    if (card.type === 'dude' || card.type === 'token') {
      this.event('play_dude', data)
    } else if (card.type === 'ruse') {
      this.event('play_ruse', data)
    }

    window.nextJob()
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
}
