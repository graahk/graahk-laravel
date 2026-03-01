import { EmoteAnimation } from "../entities/animations/EmoteAnimation"

export class Emote {
  resolve (game, event) {
    new EmoteAnimation({
      target: game.playerById(event.data.playerId),
      emote: event.data.emote,
    }).resolve()

    if (! game.isBossFight) {
      return
    }

    window.setTimeout(() => {
      new EmoteAnimation({ target: game.opponent, emote: event.data.emote }).resolve()
    }, 1000)
  }
}
