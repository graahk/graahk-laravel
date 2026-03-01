import { Boss } from './Boss'

export class ElDoradoDefensesBoss extends Boss {
  bossShouldPlayCard (card) {
    const parentShouldPlay = super.bossShouldPlayCard(card)
    if (! parentShouldPlay) return false

    // Core Charge
    if (card.id === 1288) {
      return game.currentPlayer.board.some((dude) => dude.keywords.includes('tireless'))
    }

    return true
  }
}
