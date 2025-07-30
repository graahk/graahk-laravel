<template>
  <div
    v-bind:id="`game-artifact-${artifact.uuid}`"
    class="flex flex-col gap-4 p-4 origin-center"
  >
    <div class="avatar w-full rounded-lg bg-cover bg-center transition-all duration-300 relative">
      <div
          class="avatar w-full pt-[10%] rounded-lg bg-cover bg-center transition-all duration-300 relative"
          v-bind:style="`background-image: url('${artifact.image}')`"
          v-bind:class="{
            'scale-105': artifact.glowing,
          }"
      ></div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'artifact',
  props: {
    artifact: {
      type: Object,
      required: true,
    },
  },
}
</script>
