import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import obfuscator from 'rollup-plugin-obfuscator';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // css
                'resources/css/index.css',
                'resources/css/profile.css',
                // js
                'resources/js/index.js',
                'resources/js/tinymce.js',
                'resources/js/index_.js',
                'resources/js/profile.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            plugins: [
                obfuscator({
                    options:{
                        compact: true,
                        controlFlowFlattening: true,
                        controlFlowFlatteningThreshold: 1,
                        deadCodeInjection: true,
                        deadCodeInjectionThreshold: 1,
                        debugProtection: true,
                        debugProtectionInterval: 4000,
                        disableConsoleOutput: true,
                        domainLock: [
                            'http://ddpc.test/',
                            'https://cgclound.test/',
                            'http://127.0.0.1:8000/',
                            'https://cgcx.bltn.cc/',
                            'https://cgcx.blaetoan.cyou/',
                            'https://cgcloud.test/',
                        ],
                        domainLockRedirectUrl: 'about:blank',
                        forceTransformStrings: [],
                        identifierNamesCache: null,
                        identifierNamesGenerator: 'hexadecimal',
                        identifiersDictionary: [],
                        identifiersPrefix: '',
                        ignoreImports: false,
                        inputFileName: '',
                        log: true,
                        numbersToExpressions: false,
                        optionsPreset: 'default',
                        renameGlobals: false,
                        renameProperties: false,
                        renamePropertiesMode: 'safe',
                        reservedNames: [],
                        reservedStrings: [],
                        seed: 0,
                        selfDefending: false,
                        simplify: true,
                        sourceMap: false,
                        sourceMapBaseUrl: '',
                        sourceMapFileName: '',
                        sourceMapMode: 'separate',
                        sourceMapSourcesMode: 'sources-content',
                        splitStrings: true,
                        splitStringsChunkLength: 10,
                        stringArray: true,
                        stringArrayCallsTransform: true,
                        stringArrayCallsTransformThreshold: 0.5,
                        stringArrayEncoding: [],
                        stringArrayIndexesType: [
                            'hexadecimal-number'
                        ],
                        stringArrayIndexShift: true,
                        stringArrayRotate: true,
                        stringArrayShuffle: true,
                        stringArrayWrappersCount: 1,
                        stringArrayWrappersChainedCalls: true,
                        stringArrayWrappersParametersMaxCount: 2,
                        stringArrayWrappersType: 'variable',
                        stringArrayThreshold: 0.75,
                        target: 'browser',
                        transformObjectKeys: true,
                        unicodeEscapeSequence: false
                    }
                })
            ],
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        // 保持第三方库不合并，按库拆分
                        return id.toString().split('node_modules/')[1].split('/')[0];
                    }
                }
            },
            external: [
                //'intl-tel-input/build/js/i18n/en/index.mjs',
                //'intl-tel-input/build/js/i18n/zh/index.mjs',
                //'intl-tel-input/build/js/i18n/zh_TW/index.mjs',
            ]
        }
    },
    //server: {
    //    hmr: {
    //        host: 'localhost',
    //    },
    //},
});
