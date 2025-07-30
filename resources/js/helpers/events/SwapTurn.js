import { TurnStartAnimation } from "../entities/animations/TurnStartAnimation"

export class SwapTurn {
  resolve (game) {
    game._vue.queue([
      (() => {
        game.checkTriggers('end_turn', [game.artifact, ...game.currentPlayer.board])
        window.nextJob()
      }),
      (() => {
        [game.currentPlayer, game.currentOpponent] = [game.currentOpponent, game.currentPlayer]

        game.currentPlayer.keywords = game.currentPlayer.keywords.filter((keyword) => keyword !== 'scorching')

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
    ], 'end')
  }
}
