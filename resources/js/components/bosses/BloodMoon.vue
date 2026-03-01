<template>
  <div
    class="has-tooltip absolute -top-[4rem]"
    style="width: calc(100% - 30rem)"
  >
    <!-- Gradient red to transparent top to bottom -->
    <div class="absolute inset-0 bg-gradient-to-b from-red-500 to-transparent h-[35vh] animate-moon-backdrop" />

    <div
      class="absolute inset-x-[45%] items-center justify-center"
      v-bind:id="`game-artifact-${card.uuid}`"
    >
      <img src="/images/bosses/blood-serpent-god/beams.png" class="absolute inset-0 w-full aspect-square animate-moon-spinning" />
      <img src="/images/bosses/blood-serpent-god/moon.png" class="absolute inset-0 w-full aspect-square" />
    </div>
  </div>
</template>

<script>
export default {
  name: 'blood-moon',
  props: {
    card: Object,
    highlighted: {
      type: Boolean,
      default: false,
    },
  },
};
</script>
