import { Boss } from './Boss'

export class TwoFacedDevourerBoss extends Boss {
  pushToBoard (card) {
    // Push the dude at the end of the board, but before the last dude
    this.board.splice(this.board.length - 1, 0, card)
  }
}
