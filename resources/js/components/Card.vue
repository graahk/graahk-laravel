<template>
  <div
      v-bind:data-card-id="card.id"
      class="
        graahk-card has-tooltip w-full rounded-xl overflow-hidden
        bg-cover bg-center relative
        text-black select-none aspect-[2.5/3.5]
        isolate cursor-pointer
      "
      v-bind:style="{ backgroundImage: `url('${card.image}')` }"
      v-bind:class="{
        'border border-green-500': greenBorder,
        'max-w-[10rem]': ! fullSized,
        'animate-glowing': glowing,
        'hover:scale-[1.3] hover:z-[100] hover:translate-y-[-40%] transition-all duration-200 ease-in-out': hoverState,
      }"
      v-on:click="playCard(cardKey)"
      v-on:mouseenter="checkUpdatedPower()"
  >
      <div class="absolute inset-0 rounded-xl overflow-hidden">
        <div v-if="card.level >= 4" class="z-[-1] rounded-xl overflow-hidden animate-foil"></div>
      </div>

      <img v-bind:src="`/images/cards/${card.type}-${card.level}.svg?1`" />

      <h2
        class="absolute top-[4%] left-[4%] text-center w-[14.5%] font-bold"
        v-text="card.cost"
        v-bind:class="{
          'text-green-500 font-bolder text-bordered-hard': card.cost < (card.original.cost || card.cost),
          'text-red-500 font-bolder text-bordered-hard': card.cost > (card.original.cost || card.cost),
        }"
      ></h2>

      <h3
        class="absolute top-[5%] left-[21%] w-full font-bold"
        v-text="card.name"
      ></h3>

      <span
        v-text="card.tribesText"
        v-bind:class="{
            'tribes absolute w-[80%] text-lg': true,
            'bottom-[36.5%] left-[8%]': (card.level <= 1),
            'bottom-[5.5%] left-[36.5%]': (card.level >= 2),
            'left-[8%]': card.type === 'ruse'
        }"
      ></span>

      <p
        v-if="card.text?.length > 0"
        v-html="card.text"
        v-bind:class="{
          'absolute overflow-y-auto': true,
          'bottom-[14%]': card.type !== 'ruse',
          'bottom-[11%]': card.type === 'ruse',
          'left-[9%] w-[82%] top-[65%]': (card.level <= 1),
          'left-[4%] w-[92%] p-2 rounded-lg bg-opacity-50': (card.level >= 2),
          'bg-white p-2 bg-opacity-75': (card.level === 2),
          'bg-black p-2 bg-opacity-50 text-white text-bordered-hard': (card.level >= 3),
        }"
      ></p>

      <h4
        v-text="updatedPower || power"
        class="absolute bottom-[2.6%] left-[4%] w-[29%] text-center font-bold"
        v-bind:class="{
          'text-green-500 text-bordered-hard': updatedPower || power > (card.original.power || 0),
        }"
      ></h4>
  </div>
</template>

<script>
export default {
  name: 'card',
  props: {
    card: Object,
    cardKey: Number,
    canPlay: Boolean,
    greenBorder: Boolean,
    fullSized: {
      type: Boolean,
      default: false,
    },
    glowing: {
      type: Boolean,
      default: false,
    },
    hoverState: {
      type: Boolean,
      default: false,
    },
  },
  data () {
    return {
      updatedPower: false,
    }
  },
  mounted () {
    this.card.level ??= 1
    window.resizeCards()
  },
  methods: {
    playCard (cardKey) {
      if (! this.canPlay) return

      this.$emit('play-card', cardKey)
    },
    checkUpdatedPower () {
      const effects = this.card.effects.map((e) => e.effect)

      if (effects.includes('unnamed_one')) {
        this.updatedPower = parseInt(window.game.player.graveyard.length * 50)
      }
    }
  },
  computed: {
    power () {
      this.checkUpdatedPower()

      return this.card.power
    },
  },
}
</script>
