import { TurnStartAnimation } from "../entities/animations/TurnStartAnimation"

export class SwapTurn {
  resolve (game) {
    game._vue.queue([
      (() => {
        game.checkTriggers('end_turn', game.currentPlayer.board)
        window.nextJob()
      }),
      (() => {
        [game.currentPlayer, game.currentOpponent] = [game.currentOpponent, game.currentPlayer]

        // If you are the new active player
        console.log(game.currentPlayer.id, game.player.id)
        if (game.currentPlayer.id === game.player.id) {
          new TurnStartAnimation(game).resolve(() => window.nextJob())
        } else {
          window.nextJob()
        }
      }),
      (() => {
        game.checkTriggers('start_turn', game.currentPlayer.board)
        window.nextJob()
      }),
      (() => {
        game.effect('gain_energy', { amount: 3 }, [game.currentPlayer])
        window.nextJob()
      }),
      (() => {
        game.effect('draw_cards', { amount: 1 }, [game.currentPlayer])
        window.nextJob()
      }),
      (() => {
        game.effect('ready_dudes', {}, game.currentPlayer.board)
        window.nextJob()
      }),
    ], 'end')
  }
}
