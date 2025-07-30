import { Ruse } from "../entities/Ruse"
import { reactive } from "vue"

export class PlayRuse {
  resolve (game, event) {
    let card = reactive(new Ruse(event.data.card))

    game._vue.queue([
      (async () => {
        card.owner = game.currentPlayer.id
        card.opponent = game.currentOpponent.id

        card.glowing = false

        game._vue.$refs.display.setCard(card)
        game.currentPlayer.hand = game.currentPlayer.hand.filter((c, key) => key !== event.data.key)
        game.currentPlayer.energy -= card.cost

        await timeout(card.enterSpeed || 500)

        window.nextJob()
      }),
      (() => {
        // Trigger the effects of the ruse
        game.checkTriggers('cast_ruse', [card], game.getTargets('from_uuid', null, event.data.target), card)
        window.nextJob()
      }),
      (async () => {
        await timeout(1000)
        game._vue.$refs.display.setCard(null)
        game.currentPlayer.graveyard.push(card)
        window.nextJob()
      }),
      (() => {
        game.checkTriggers('play_ruse', [window.game.artifact, ...game.currentPlayer.board, ...game.currentOpponent.board], card)
        window.nextJob()
      }),
      (() => {
        game.checkTriggers('player_play_ruse', game.currentPlayer.board, card)
        window.nextJob()
      }),
      (() => {
        game.checkTriggers('opponent_play_ruse', game.currentOpponent.board, card)
        window.nextJob()
      }),
    ])
  }
}
