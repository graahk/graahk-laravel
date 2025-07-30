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
      v-bind:style="{ backgroundImage: `url('${card.image}')` }"
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
      }"
    ></div>
      <!-- <div class="absolute opacity-50">
        <span v-text="card.uuid" />
      </div> -->

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
          v-for="(keyword, key) in card.keywords.filter((effect) => ! ['rush', 'scenery', 'innumerable'].includes(effect))"
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
export default {
  name: 'card',
  props: {
    card: Object,
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
