<template>
  <div
    v-on:mouseleave="emotesOpen = false"
    class="flex flex-col gap-4 p-4 origin-center"
    v-bind:class="{
      'flex-col-reverse': reverse,
    }"
  >
    <div class="avatar w-full rounded-lg bg-cover bg-center transition-all duration-300 relative">
      <img
        v-for="(keyword, key) in player.keywords"
        v-bind:key="key"
        v-bind:src="`/images/visual-effects/player__${keyword}.png`"
        class="absolute inset-0 w-full z-10"
        v-bind:class="`visual-effect-${keyword}`"
      />

      <div
          class="avatar w-full pt-[100%] rounded-lg bg-cover bg-center transition-all duration-300 relative"
          v-on:click="openEmoteMenu"
          v-bind:style="`background-image: url('${player.avatar}')`"
          v-bind:class="{
            'scale-105': player.glowing,
          }"
      >
        <div
          v-if="emotesOpen && reverse"
          style="z-index: 300"
          class="
            absolute -inset-x-3 -top-[36vh] h-[35vh]
            border border-border p-4 bg-black bg-opacity-50 rounded-lg
            overflow-y-auto
          "
        >
          <div class="grid grid-cols-2 gap-2 w-full">
            <div
              v-for="(emote, index) in emotes"
              v-bind:key="index"
              class="aspect-square"
              v-bind:class="`emote-${emote}`"
              v-on:click="performEmote(emote)"
            >
              <img v-bind:src="`/images/animations/emotes/${emote}.png`" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- <div class="absolute opacity-50">
      <span v-text="player.uuid" />
    </div> -->

    <h2
      class="w-full text-center font-bold text-6xl"
      v-text="player.power"
    ></h2>

    <div class="flex">
      <div class="relative" ref="handsize">
        <img src="/images/icons/handsize.jpg" alt="">
        <p
          class="absolute inset-0 text-4xl font-bold items-center justify-center flex"
          v-text="player.hand.length"
        ></p>
      </div>

      <div class="relative" ref="decksize">
        <img src="/images/icons/decksize.jpg" alt="">
        <p
          v-text="player.deck.length"
          v-bind:class="{
            'absolute inset-0 text-4xl font-bold items-center justify-center flex': true,
            'text-red-500 text-bordered-hard': player.deck.length <= 5,
          }"
        ></p>
      </div>

      <div class="relative" ref="energy">
        <img src="/images/icons/energy.jpg" alt="">
        <p
          class="absolute inset-0 text-4xl font-bold items-center justify-center flex"
          v-text="player.energy"
        ></p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'player',
  props: {
    player: Object,
    reverse: {
      type: Boolean,
      default: false,
    }
  },
  data () {
    return {
      emotesOpen: false,
      emotes: [
        'thumbs-up',
        'who-me',
        'spoidersus',
        'spoidershy',
        'sailorcry',
      ],
    }
  },
  methods: {
    openEmoteMenu() {
      if (! this.reverse || this.$parent.$refs.targeting?.areTargeting()) {
        return
      }

      this.emotesOpen = ! this.emotesOpen
    },
    performEmote(emote) {
      window.game.event('emote', {
        emote: emote,
        playerId: this.player.id,
      })
    },
  }
}
</script>
