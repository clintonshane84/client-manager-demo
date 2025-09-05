import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        })
    ],
	ssr: {
		noExternal: ['@inertiajs/server', '@inertiajs/vue3'],
	},
	build: {
	  rollupOptions: {
	    output: {
	      // make output predictable; Sail command expects a single entry
	      // Not strictly required, but nice to have.
	    },
	  },
	},
});
