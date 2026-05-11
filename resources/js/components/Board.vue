<template>
  <div
    class="flex flex-col h-[40vh] w-full justify-center transition-all duration-500"
    v-bind:class="{ 'bg-surface': ! active, 'bg-background': active }"
  >
    <slot></slot>
  </div>
</template>

<script>
export default {
  name: 'board',
  props: {
    board: Object,
    active: Boolean,
  },
}
</script>
