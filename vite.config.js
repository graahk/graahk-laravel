import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import mkcert from 'vite-plugin-mkcert'
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  server: {
    https: true,
    host: '0.0.0.0',
    hmr: {
      host: '192.168.0.131'
    },
  },
  plugins: [
    mkcert(),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
    laravel({
      input: [
        'resources/css/app.scss',
        'resources/js/app.js'
      ],
    }),
  ],
  resolve: {
    alias: {
      vue: 'vue/dist/vue.esm-bundler.js',
    },
  },
});
