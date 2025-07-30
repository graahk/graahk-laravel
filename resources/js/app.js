import './fit-text';
import './tooltipper';

import Pusher from 'pusher-js';
import Play from './components/Play.vue';
import { createApp } from 'vue';
import axios from 'axios';
import { v5 as uuid } from 'uuid';

// Generates an UUID v5 based on a base UUID and a namespace
window.uuid = ((base) => uuid(`${base}`, '7b38091b-5e98-42f0-ad73-ac981fc83211'))

window.axios = axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

window.pusher = new Pusher(
  'b8382356f8042afa07bc',
  { cluster: 'eu' }
)

window.timeout = (delay) => {
  return new Promise((resolve) => setTimeout(resolve, delay))
}

window.requiresTarget = [
  'target_dude',
  'target_anything',
  'target_hand',
  'target_player',
]

window.triggerRequiresTarget = [
  'enter_field',
  'cast_ruse',
]

window.shouldRefetchTargets = [
  'all',
  'all_dudes',
  'all_players',
  'all_hand',
]

document.addEventListener('DOMContentLoaded', () => {
  window.closeModal = () => {
    Livewire.dispatch('closeModal')
  }

  window.openModal = (modal, params) => {
    Livewire.dispatch('openModal', [modal, params])
  }

  window.addEventListener('close-modal', () => {
    window.closeModal()
  })
})

Livewire.hook('commit', ({ succeed }) => {
  succeed(() => {
    window.setTimeout(() => window.mountApp(), 500)
  })
})

window.mountApp = (() => {
  if (! document.getElementById('app')) return
  createApp({}).component('Play', Play).mount('#app')
})

window.mountApp()
