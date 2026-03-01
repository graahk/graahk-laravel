<template>
  <div v-if="animation._meta" class="relative" style="z-index: 600;">
    <div
      v-if="animation._meta.jaws"
      class="absolute"
      v-bind:id="`jaws-${animation.uuid}`"
      v-bind:key="animation.uuid"
      v-bind:style="{
        height: animation._meta.jaws.height + 'px',
        width: animation._meta.jaws.width + 'px',
        top: animation._meta.jaws.top + 'px',
        left: animation._meta.jaws.left + 'px',
      }"
    >
      <img
        class="absolute animation-great-devouring-left"
        v-bind:src="`/images/animations/jaw.png?r=${animation.uuid}`"
        v-bind:key="animation.uuid"
        v-bind:style="{
          top: '0px',
          left: '0px',
          height: animation._meta.jaws.height + 'px',
        }"
      >

      <img
        class="absolute animation-great-devouring-right"
        v-bind:src="`/images/animations/jaw.png?r=${animation.uuid}`"
        v-bind:key="animation.uuid"
        v-bind:style="{
          top: '0px',
          right: '0px',
          height: animation._meta.jaws.height + 'px',
          transform: 'scaleX(-1)',
        }"
      >
    </div>

    <img
      v-if="animation._meta.image"
      class="absolute"
      v-bind:src="`/images/animations/${animation._meta.image}.png?r=${animation.uuid}`"
      v-bind:key="animation.uuid"
      v-bind:id="animation.uuid"
      v-bind:style="{
        top: animation._meta.y + 'px',
        left: animation._meta.x + 'px',
        width: animation._meta.width + 'px',
      }"
    >

    <img
      v-if="animation._meta.emote"
      class="absolute animate-emote"
      v-bind:src="`/images/animations/${animation._meta.emote}.png?r=${animation.uuid}`"
      v-bind:key="animation.uuid"
      v-bind:id="animation.uuid"
      v-bind:style="{
        top: animation._meta.y + 'px',
        left: animation._meta.x + 'px',
        width: animation._meta.width + 'px',
      }"
    >

    <div
      v-if="animation._meta.class"
      class="absolute"
      v-bind:class="animation._meta.class"
      v-bind:key="animation.uuid"
      v-bind:style="{
        top: animation._meta.y + 'px',
        left: animation._meta.x + 'px',
      }"
    ></div>

    <img
      v-if="animation._meta.projectile"
      class="absolute animate-projectile"
      v-bind:id="`projectile-${animation.uuid}`"
      v-bind:class="animation._meta.class"
      v-bind:src="`/images/projectiles/${animation._meta.projectile}.png?r=${animation.uuid}2`"
      v-bind:key="animation.uuid"
      v-bind:style="{
        width: animation._meta.width + 'px',
        top: animation._meta.y + 'px',
        left: animation._meta.x + 'px',
        rotate: animation._meta.rotate + 'deg',
      }"
    />
  </div>
</template>

<script>
export default {
  name: 'animation',
  props: {
    animation: Object,
  },
}
</script>
