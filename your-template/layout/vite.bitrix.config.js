import * as dotenv from 'dotenv'
dotenv.config()

import { defineConfig } from 'vite'

import autoprefixer from "autoprefixer";
import liveReload from 'vite-plugin-live-reload'

export default defineConfig({
    base: process.env.TEMPLATE_PUBLIC_PATH,
    plugins: [
        liveReload('../../../**/*.php'),
    ],
    css: {
      postcss: {
          plugins: [
              autoprefixer,
          ]
      }
    },
    build: {
        assetsDir: '.',
        copyPublicDir: false,
        rollupOptions: {
            input: {
                main: 'src/main.js',
            },
            output: {
                entryFileNames: 'main.js',
                assetFileNames: 'main.css',
            },
        },
    },
    server: {
        strictPort: true,
    }
})