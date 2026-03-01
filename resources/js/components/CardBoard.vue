<template>
  <div
    :id="card.type + '-' + card.uuid"
    class="card-board has-tooltip relative flex justify-center origin-center transition-all duration-500"
    v-bind:data-card-id="card.id"
    v-bind:class="{
      'scale-105': card.glowing,
    }"
  >
    <div
      v-bind:style="{ backgroundImage: specialArt ? null : `url('${card.image}')` }"
      class="
        w-[10rem] aspect-[2.5/3.5]
        border-[2px] bg-cover bg-center overflow-hidden
      "
      v-bind:class="{
        'rounded-xl': card.type !== 'token',
        'rounded-full': card.type === 'token',
        'border-white': card.ready,
        'border-black': ! card.ready,
        '!border-green-500': card.highlighted,
        '!border-0': specialArt,
      }"
    ></div>
    <!-- <div class="absolute opacity-50">
      <span v-text="card.uuid" />
    </div> -->

    <div
      v-if="specialArt"
      class="absolute -bottom-[1rem] inset-x-0 -mx-[1rem]"
    >
      <img v-bind:src="specialArt" class="w-full animate-bobbing" />
    </div>

    <div
      v-if="card.level >= 4"
      class="overflow-hidden animate-foil"
      v-bind:class="{
        'rounded-xl': card.type !== 'token',
        'rounded-full': card.type === 'token',
      }"
    ></div>

    <div class="absolute -inset-[2rem] pointer-events-none">
      <TransitionGroup name="debuff">
        <img
          v-for="(debuff, key) in card.debuffs"
          v-bind:key="key"
          v-bind:src="`/images/visual-effects/${debuff.visual}.png`"
          class="absolute inset-0 w-full"
          v-bind:class="`visual-effect-${debuff.visual}`"
        />
      </TransitionGroup>

      <img
        v-for="(keyword, key) in card.keywords.filter((effect) => ! ['rush', 'scenery', 'innumerable', 'delicate', 'life_linked'].includes(effect))"
        v-bind:key="key"
        v-bind:src="`/images/visual-effects/${keyword}.png`"
        class="absolute inset-0 w-full"
        v-bind:class="
          `visual-effect-${keyword} `
          + (card.keywords.includes('tireless') && card.power <= 0 ? 'opacity-50' : '')
        "
      />
    </div>

    <div
      class="
          absolute -bottom-[2rem] pb-1 pt-2 px-6 text-5xl font-bold bg-surface
          border-[2px] border-black rounded-2xl overflow-hidden
      "
      v-if="(card.power !== null && card.power !== undefined)"
      v-bind:class="{
        'border-white': card.ready,
        'border-black': ! card.ready,
        '!border-green-500': card.highlighted,
      }"
    >
      <span
        v-if="card.keywords.includes('scenery')"
        class="visual-effect-scenery z-0"
      />

      <span
        v-text="card.power"
        v-bind:class="{
          'z-10 relative': true,
          'text-green-500': card.original.power < card.power,
          'text-red-500': card.dead || (card.original.power > card.power),
        }"
      />
    </div>
  </div>
</template>

<script>
import { Dude } from '../helpers/entities/Dude';

export default {
  name: 'card',
  props: {
    card: Object,
  },
  data () {
    return {
      specialArt: null,
    }
  },
  mounted () {
    // Two-Headed Devourer
    if ([1283].includes(this.card.id)) this.specialArt = '/images/bosses/two-headed-devourer/acid.png'
    if ([1285].includes(this.card.id)) this.specialArt = '/images/bosses/two-headed-devourer/withering.png'
  },
};
</script>

<style scoped>
.debuff-enter-active,
.debuff-leave-active {
  transition: all 0.5s ease;
}

.debuff-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}

.debuff-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>
