import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/scss/app.scss',
                'resources/css/profile.css',
                'resources/js/index.js',
                'resources/js/tinymce.js',
                'resources/js/index_.js',
                'resources/js/profile.js',
            ],
            refresh: true,
            detectTls: 'vps.bltn.cc',
        }),
    ],
    build: {
        // 1. 使用 esbuild 壓縮，速度最快且產物輕量
        minify: 'esbuild', 
        // 2. 移除 Source Map 減少生產環境體積（除非你需要線上除錯）
        sourcemap: false,
        // 3. 調整資源塊大小警告閥值
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                // 4. 精細化分塊策略：將大型套件獨立出來，增加瀏覽器快取命中率
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        // TinyMCE 這種大型套件強烈建議獨立出來
                        if (id.includes('tinymce')) {
                            return 'vendor-tinymce';
                        }
                        // 其餘常用的第三方庫合併為一個 vendor
                        return 'vendor';
                    }
                },
                // 5. 讓輸出的檔名更簡潔
                chunkFileNames: 'assets/[name]-[hash].js',
                entryFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash].[ext]',
            },
        },
    },
    server: {
        cors: true,
        host: '0.0.0.0',
        hmr: {
            host: 'vps.bltn.cc',
            protocol: 'wss',
        },
    },
});
