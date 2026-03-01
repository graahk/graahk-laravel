import { Dude } from "../entities/Dude"
import { reactive } from "vue"
import { Token } from "../entities/Token"
import { HandleAnimation } from "../entities/animations/HandleAnimation"

export class PlayDude {
  resolve (game, event) {
    let card

    if (event.data.card.type === 'token') {
      card = reactive(new Token(event.data.card))
    } else {
      card = reactive(new Dude(event.data.card))
    }

    game._vue.queue([
      (async () => {
        card.owner = game.currentPlayer.id
        card.opponent = game.currentOpponent.id

        card.ready = card.keywords.includes('rush')
        card.glowing = false

        game.currentPlayer.pushToBoard(card)
        game.currentPlayer.hand = game.currentPlayer.hand.filter((c, key) => key !== event.data.key)
        game.currentPlayer.energy -= card.cost

        if (card.entranceAnimation?.animation) {
          window.setTimeout(() => {
            new HandleAnimation(card, null, card.entranceAnimation, card).resolve(() => {
              window.nextJob()
            })
          }, 1)
        } else {
          await timeout(card.enterSpeed || 0)
          window.nextJob()
        }
      }),
      (() => {
        game.checkTriggers('enter_field', [card], game.getTargets('from_uuid', null, event.data.target))
        window.nextJob()
      }),
      (() => {
        if (card instanceof Dude) {
          game.checkTriggers('play_dude', [game.artifact, ...game.currentPlayer.board, ...game.currentOpponent.board].filter((c) => c && c.uuid !== card.uuid), card)
        }

        window.nextJob()
      }),
      (() => {
        if (card instanceof Dude) {
          game.checkTriggers('player_play_dude', game.currentPlayer.board.filter((c) => c.uuid !== card.uuid), card)
        }

        window.nextJob()
      }),
      (() => {
        if (card instanceof Dude) {
          game.checkTriggers('opponent_play_dude', game.currentOpponent.board.filter((c) => c.uuid !== card.uuid), card)
        }

        window.nextJob()
      }),
    ])
  }
}
