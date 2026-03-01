<template>
  <div
    class="w-full h-[86vh] inset-[-2rem] absolute pointer-events-none flex justify-end items-center"
    style="z-index: 1000"
  >
    <Transition name="display">
      <div
        class="aspect-[2.5/3.5] w-[20rem]"
        v-if="card"
        :id="card ? card.type + '-' + card.uuid : null"
      >
        <Card :card="card" full-sized />
      </div>
    </Transition>
  </div>
</template>

<script>
import Card from './Card.vue'

export default {
  name: 'card',
  components: {
    Card,
  },
  data () {
    return {
      card: null,
    }
  },
  methods: {
    setCard (card) {
      this.card = card
    },
  },
};
</script>

<style scoped>
.display-enter-active,
.display-leave-active {
  transition: all 0.5s ease;
}

.display-enter-from {
  opacity: 0;
  transform: translateX(30px);
}

.display-enter-to,
.display-leave-from {
  opacity: 1;
  transform: translateX(0);
}

.display-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}
</style>
