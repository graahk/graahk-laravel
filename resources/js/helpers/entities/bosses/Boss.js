import { Dude } from '../Dude'
import { Player } from '../Player'
import { Token } from '../Token'

export class Boss extends Player {
  playNPCTurn () {
    game._vue.queue([
      (() => {
        let found = false

        // Check for cards that are playable
        for (let i = 0; i < this.hand.length; i++) {
          const card = this.hand[i]

          if (card.cost > this.energy || ! this.bossShouldPlayCard(card))
            continue

          game._vue.queue([
            (() => {
              window.game.playCard(i)
              window.nextJob()
            }),
            (() => window.setTimeout(() => {
              this.playNPCTurn()
              window.nextJob()
            }, 2000)),
          ], 'end')

          found = true
          break
        }

        // Check for cards that can attack
        if (! found) {
          for (let i = 0; i < this.board.length; i++) {
            const attacker = this.board[i]

            if (! attacker.ready || attacker.dead || attacker.power === 0)
              continue

            // Dudes with more power kill tougher opponents first
            let defender = null

            const opponentPower = game.currentOpponent.board.reduce((power, dude) => power + dude.power, 0)
            const playerPower = game.currentPlayer.board
              .filter((dude) => ! dude.keywords.includes('scenery'))
              .reduce((power, dude) => power + dude.power, 0)

            const hasProtector = game.currentOpponent.board.some((dude) => dude.keywords.includes('protect') && dude.power > 0)
            const hasAdvantage = (opponentPower + 100) < playerPower
            const canBeKilled = opponentPower >= game.currentPlayer.power

            if (hasProtector) {
              defender = game.currentOpponent.board
                .filter((dude) => dude.power > 0 && ! dude.dead)
                .filter((dude) => dude.keywords.includes('protect'))
                .sort((dude) => dude.power)
                .reverse()[0]
            } else {
              if (playerPower >= game.currentOpponent.power) {
                // If we have more power than the opponent, attack the opponent directly for the kill
                defender = game.currentOpponent
              }

              if (! canBeKilled && hasAdvantage) {
                defender = game.currentOpponent
              } else {
                defender = game.currentOpponent.board
                  .filter((dude) => dude.power > 0 && ! dude.dead)
                  .filter((dude) => ! hasProtector || ! dude.keywords.includes('protect'))
                  .filter((dude) => ! (dude.id === 1174 && dude.keywords.includes('scenery'))) // Timed Explosives
                  .sort((dude) => dude.power)
                  .reverse()

                defender = (defender && defender.length > 0) ? defender[0] : game.currentOpponent
              }
            }

            if (defender) {
              if (! this.bossShouldAttack(attacker, defender)) continue

              game._vue.queue([(() => {
                  game.event('attack', { attacker: attacker.uuid, defender: defender.uuid })
                  window.nextJob()
                }),
                (() => window.setTimeout(() => {
                  this.playNPCTurn()
                  window.nextJob()
                }, 2000)),
              ], 'end')

              found = true
              break
            }
          }
        }

        if (! found) {
          game._vue.queue([(() => {
            window.setTimeout(() => {
              window.game.event('end_turn')
              window.nextJob()
            }, 1000)
          })], 'end')
        }

        window.nextJob()
      })
    ], 'end')

    window.nextJob()
  }

  bossShouldPlayCard (card) {
    // Battle Encourager
    if (card.id === 57) {
      return game.currentPlayer.board.filter(d => d instanceof Dude && ! (d instanceof Token)).length > 0
    }

    // Timed Explosives
    if (game.currentPlayer.board.some((dude) => dude.id === 1174)) {
      return card.power !== 100
    }

    // Lone Warrior
    const warrior = game.currentOpponent.board.filter(e => e.id === 102)[0] ?? null
    if (warrior) {
      // Don't play if we can kill the warrior first
      return (this.currentPowerOnBoard() < warrior.power)
        || (this.currentPowerOnBoard() > 0 && warrior.keywords.includes('withering'))
    }

    return true
  }

  bossShouldAttack (attacker, defender) {
    // Phantom Protector
    if (defender.id === 58) {
      return game.currentPlayer.board.some((dude) => dude.power > defender.power && dude.ready && ! dude.keywords.includes('scenery'))
    }

    // Cool Jack
    const jack = game.currentOpponent.board.filter(e => e.id === 34)[0] ?? null
    if (jack && ! defender.keywords.includes('withering')) {
      // Have at least 200 power on the board
      return this.currentPowerOnBoard() >= jack.power
    }

    // Deep Sea Fish
    if (game.currentPlayer.board.some((dude) => dude.id === 62 && dude.ready)) {
      return attacker.id === 62
    }

    return true
 }

  currentAttackersOnBoard () {
    return game.currentPlayer.board
      .filter((dude) => dude instanceof Dude && dude.ready && ! dude.keywords.includes('scenery'))
  }

  currentPowerOnBoard () {
    return this.currentAttackersOnBoard().reduce((power, dude) => power + dude.power, 0)
  }
}
