import { TurnStartAnimation } from "../entities/animations/TurnStartAnimation"

export class Mulliganed {
  async resolve (game, event) {
    let player = game.playerById(event.data.player)
    player.mulliganed = event.data.amount

    if (player.id !== game._vue.playerId) {
      // event.data.deck is a list of UUIDs, sort the deck based on them
      player.deck = player.deck.sort((card) => event.data.deck.indexOf(card.uuid))
      player.hand = player.deck.splice(0, 5) // Draw 5 cards
    }

    if (game.isBossFight) {
      // Draw cards for your opponent
      //Randomize the deck order
      game.opponent.deck = game.opponent.deck.sort(() => Math.random() - 0.5)
      game.opponent.hand = game.opponent.deck.splice(0, 5) // Draw 5 cards
    }

    if (game.haveMulliganed()) {
      const data = {
        text: (game.areCurrentPlayer() ? 'You go first!' : 'Opponent goes first!')
      }

      await new TurnStartAnimation().resolve(() => {
        game.currentPlayer.gain_energy({ amount: 2 })
      }, data)

      game.updateGameState()
    }
  }
}
