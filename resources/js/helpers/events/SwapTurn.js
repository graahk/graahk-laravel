import { TurnStartAnimation } from "../entities/animations/TurnStartAnimation"

export class SwapTurn {
  resolve (game) {
    game._vue.queue([
      (() => {
        game.checkTriggers('end_turn', [game.artifact, ...game.currentPlayer.board])

        if (game.isBossFight && ! game.areCurrentPlayer()) {
          game.checkTriggers('boss_end_turn', [game.artifact])
        }

        window.nextJob()
      }),
      (() => {
        [game.currentPlayer, game.currentOpponent] = [game.currentOpponent, game.currentPlayer]

        game.currentPlayer.keywords = game.currentPlayer.keywords.filter((keyword) => keyword !== 'scorching')
        game.currentPlayer.doubledRuses = 0

        // If you are the new active player
        if (game.currentPlayer.id === game.player.id) {
          new TurnStartAnimation(game).resolve(() => window.nextJob())
        } else {
          window.nextJob()
        }
      }),
      (() => {
        game.currentPlayer.board.forEach((dude) => {
          if (dude.willClearStuns) {
            dude.debuffs = dude.debuffs.filter((debuff) => debuff.type !== 'stun')
            dude.willClearStuns = false
          }
        })

        game.checkTriggers('start_turn', [game.artifact, ...game.currentPlayer.board.filter((d) => d.dead === false)])

        if (game.isBossFight && ! game.areCurrentPlayer()) {
          game.checkTriggers('boss_start_turn', [game.artifact])
        }

        window.nextJob()
      }),
      (() => {
        game.currentPlayer.gain_energy({ amount: 3 })
        window.nextJob()
      }),
      (() => {
        game.currentPlayer.drawsThisTurn = 0
        game.currentPlayer.draw_cards({ amount: 1 })
        window.nextJob()
      }),
      (() => {
        game.currentPlayer.ready_dudes()
        window.nextJob()
      }),
      (() => {
        // Play the boss turn, if applicable
        if (! game.isBossFight || game.areCurrentPlayer()) {
          window.nextJob()
          return
        }

        window.setTimeout(() => {
          game.currentPlayer.playNPCTurn()
        }, 1000)
      }),
    ], 'end')
  }
}
