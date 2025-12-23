import antfu from '@antfu/eslint-config';
import globals from 'globals';

export default antfu(
    {
        type: 'app',

        // Disable TypeScript, Vue, etc. since this is vanilla JS
        typescript: false,
        vue: false,
        react: false,
        jsonc: false,
        yaml: false,
        markdown: false,

        ignores: [
            'vendor/**',
            'tests/**',
            'docs/**',
            'resources/vendor/**'
        ],

        // Stylistic formatting rules
        stylistic: {
            indent: 4,
            quotes: 'single',
            semi: true
        }
    },

    // Custom rules for the project
    {
        rules: {
            // Allow console in debug library
            'no-console': 'off',

            // Allow unused vars with _ prefix or Widget suffix
            'unused-imports/no-unused-vars': ['error', {
                args: 'none',
                varsIgnorePattern: '^(_|.*Widget)$',
                caughtErrors: 'none'
            }],
            'no-unused-vars': ['error', {
                args: 'none',
                varsIgnorePattern: '^(_|.*Widget)$',
                caughtErrors: 'none'
            }],

            // Code style
            'style/brace-style': ['error', '1tbs'],
            'style/comma-dangle': ['error', 'never'],
            'style/no-mixed-operators': 'off',
            'style/max-statements-per-line': 'off',

            // Relax some rules for legacy patterns
            'no-prototype-builtins': 'off',
            'no-sequences': 'off',
            'no-unused-expressions': 'off',
            'no-use-before-define': ['error', { functions: false, classes: true, variables: true }],
            'unicorn/no-array-for-each': 'off',

            // JSDoc relaxed rules
            'jsdoc/require-returns-description': 'off',
            'jsdoc/check-param-names': 'off',

            // Allow function expressions (for Widget.extend pattern)
            'func-style': 'off',
            'antfu/consistent-list-newline': 'off',

            // Modern JavaScript requirements
            'prefer-const': 'error',
            'no-var': 'error',
            'prefer-arrow-callback': 'warn',
            'prefer-template': 'warn',
            'object-shorthand': 'warn'
        }
    },

    // Custom config for resources folder
    {
        files: ['resources/**/*.js'],
        languageOptions: {
            ecmaVersion: 2020,
            sourceType: 'script',
            globals: {
                ...globals.browser,
                PhpDebugBar: 'writable',
                hljs: 'readonly'
            }
        }
    }
);
