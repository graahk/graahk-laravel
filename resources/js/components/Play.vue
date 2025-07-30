<template>
  <div class="flex h-screen w-screen overflow-hidden relative">
    <Mulligan
      v-if="! game.haveMulliganed()"
      v-bind:player="game.player"
      v-bind:opponent="game.opponent"
      v-bind:game-id="gameId"
      ref="mulligan"
    />

    <GameFinished
      v-if="gameCompleted"
      ref="game_over"
    />

    <Display ref="display" />

    <div
      class="flex h-screen w-screen overflow-hidden relative"
      v-if="game.haveMulliganed()"
    >
      <Errors ref="errors" />

      <div
        class="absolute inset-0 pointer-events-none"
        style="z-index: 800"
      >
        <Animation
          v-for="(animation, key) in animations"
          :key="key"
          :animation="animation"
        />
      </div>

      <div
        class="flex flex-col gap-4 h-screen w-[15rem] bg-surface border-r border-r-border justify-between"
        style="z-index: 100"
      >
        <Player
          :player="game.opponent"
          v-on:click="target(game.opponent)"
          :ref="'player-' + game.opponent.uuid"
        />

        <Artifact
          v-if="game.artifact"
          :artifact="game.artifact"
          v-on:click="target(game.artifact)"
          ref="game-artifact"
        />

        <Player
          :player="game.player"
          :reverse="true"
          v-on:click="target(game.player)"
          :ref="'player-' + game.player.uuid"
        />
      </div>

      <div
        class="flex flex-col grow"
        style="z-index: 200"
      >
        <Board
          :board="game.opponent.board"
          :active="! game.areCurrentPlayer()"
          :ref="'board-' + game.opponent.uuid"
        >
          <TransitionGroup
            class="flex flex-wrap h-[30vh] w-full gap-4 items-center justify-evenly"
            name="dude"
            tag="div"
          >
            <CardBoard
              v-for="card in game.opponent.board"
              v-bind:key="card.uuid"
              :card="card"
              v-on:click="target(card)"
            />
          </TransitionGroup>
        </Board>

        <Board
          :board="game.player.board"
          :active="game.areCurrentPlayer()"
          :ref="'board-' + game.player.uuid"
        >
          <TransitionGroup
            class="flex flex-wrap h-[30vh] w-full gap-4 items-center justify-evenly"
            name="dude"
            tag="div"
          >
            <CardBoard
              v-for="card in game.player.board"
              v-bind:key="card.uuid"
              :card="card"
              v-on:click="target(card)"
            />
          </TransitionGroup>
        </Board>

        <div
          class="relative flex justify-evenly h-[20vh] border-t border-t-border bg-surface"
          style="z-index: 200"
        >
          <div
            class="
              absolute z-10 left-0 right-0 bottom-0 -mb-[50px] flex justify-center items-center gap-2
              transition-all duration-500 ease-in-out opacity-100
            "
            v-bind:class="{
              'opacity-50 hover:opacity-100': ! canDoAnything(),
            }"
          >
            <TransitionGroup name="card">
              <Card
                v-for="(card, key) in game.player.hand"
                v-bind:key="card.uuid"
                :id="'hand-' + card.uuid"
                :card="card"
                :card-key="key"
                :green-border="canDoAnything() && card.cost <= game.player.energy"
                :can-play="canDoAnything() && (card.cost <= game.player.energy || areTargeting())"
                v-on:play-card="playCard"
                :glowing="card.glowing"
                hover-state
              />
            </TransitionGroup>
          </div>
        </div>
      </div>

      <div class="flex flex-col h-screen w-[15rem] bg-surface border-l border-l-border">
        <Targeting ref="targeting" />

        <div class="flex items-center justify-center w-full h-[80vh]">
          <button
            v-on:click="endTurn()"
            v-bind:class="{
              'bg-green-500 hover:bg-green-600 cursor-pointer': canDoAnything(),
              'bg-gray-500 cursor-not-allowed': ! canDoAnything(),
            }"
            class="block rounded px-6 py-3 font-bold text-2xl text-surface"
          >
            End Turn
          </button>
        </div>

        <div class="flex items-center justify-center w-full h-[20vh]">
          <button
            v-on:click="surrender()"
            class="
              bg-red-500 hover:bg-red-600 cursor-pointer
              block rounded px-4 py-2 font-bold text-surface
            "
          >
            Surrender
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Card from './Card.vue'
import Artifact from './Artifact.vue'
import Board from './Board.vue'
import CardBoard from './CardBoard.vue'
import Player from './Player.vue'
import Display from './Display.vue'
import Animation from './animations/Animation.vue'
import Targeting from './Targeting.vue'
import Errors from './Errors.vue'
import Mulligan from './Mulligan.vue'
import GameFinished from './GameFinished.vue'
import { Game } from '../helpers/game'
import { Queue } from '../helpers/entities/Queue'

import { reactive } from 'vue'

export default {
  name: 'game',
  components: {
    Card,
    Artifact,
    Board,
    CardBoard,
    Player,
    Display,
    Animation,
    Targeting,
    Errors,
    Mulligan,
    GameFinished,
  },
  props: {
    startingGameState: String,
    playerId: Number,
    gameId: String,
  },
  data () {
    return {
      jobs: [],
      animations: [],
      game: null,
      gameState: JSON.parse(this.startingGameState),
      gameCompleted: false,
    }
  },
  created () {
    this.game = window.game = new Game(this)
    this.jobs = window.jobs = reactive(new Queue())

    this.gameCompleted = this.game.completed

    this.$nextTick(() => window.resizeCards())
  },
  methods: {
    // Add events to the queue
    async queue (events, pos = 'start') {
      window.jobs.push(events, pos)

      if (window.jobs.isProcessing) return
      window.jobs.processQueue()
    },
    target (target) {
      this.$refs.targeting.target(target)
    },
    areTargeting () {
      return this.$refs.targeting?.areTargeting()
    },
    playCard (cardKey, data = {}) {
      if (! this.canDoAnything()) return

      let card = this.game.player.hand[cardKey]
      if (this.areTargeting()) {
        this.$refs.targeting.target(card)
      } else {
        const requiresTarget = card.effects.map((c) => c.target).some((t) => window.requiresTarget.includes(t))
          && card.effects.map((c) => c.trigger).some((t) => window.triggerRequiresTarget.includes(t))

        if (! requiresTarget) {
          return this.game.playCard(cardKey, data)
        }

        this.$refs.targeting.setAimer(card)
      }
    },
    getTarget () {
      return this.$refs.targeting?.victim || false
    },
    endTurn () {
      if (! this.canDoAnything() || this.areTargeting()) return
      this.game.event('end_turn')
    },
    effect (effect, data, target = null) {
      this.game.effect(effect, data, target)
    },
    canDoAnything () {
      return this.game.areCurrentPlayer()
          && this.jobs.checkQueueEmpty()
          && ! this.gameCompleted
    },
    surrender () {
      if (window.confirm('Are you sure you want to surrender?')) {
        this.game.event('surrender', {
          winner: this.game.opponent.uuid,
          loser: this.game.player.uuid,
        })
      }
    }
  },
}
</script>

<style scoped>
/* Dudes on the field */
.dude-enter-active,
.dude-leave-active {
  transition: all 0.5s ease;
}

.dude-enter-from {
  opacity: 0;
  transform: translateX(30px);
}

.dude-leave-to {
  opacity: 0;
  transform: translateY(100px) rotate(30deg) scale(0.5);
}

/* Cards in hand */
.card-enter-active,
.card-leave-active {
  transition: all 0.5s ease;
}

.card-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}

.card-leave-to {
  opacity: 0;
  width: 0%;
  transform: translateY(-100px);
}
</style>
