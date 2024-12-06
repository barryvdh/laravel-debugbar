import { viteBundler } from '@vuepress/bundler-vite';
import { defaultTheme } from '@vuepress/theme-default';
import { defineUserConfig } from 'vuepress';

export default defineUserConfig({
  base: '/',
  title: 'Laravel Debugbar',
  description: 'Laravel Debugbar integrates PHP Debug Bar with Laravel',

  bundler: viteBundler(),
  head: [
    ['link', {rel: "stylesheet", href: '/debugbar.css'}],
    ['script', {src: '/debugbar.js'}],
  ],
  theme: defaultTheme({
    // logo: '/images/logo.png',
    navbar: [
      { text: 'Home', link: '/' },
      { text: 'Guide', link: '/guide/' },
      { text: 'Configuration', link: '/guide/configuration' },
      { text: 'Collectors', link: '/guide/collectors' },
      { text: 'FAQ', link: '/guide/faq' },
    ],
    sidebar: {
      '/guide/': [
        {
          text: 'Guide',
          children: [
            '/guide/README.md',
            '/guide/installation.md',
            '/guide/configuration.md',
            '/guide/collectors.md',
            '/guide/faq.md',
          ],
        },
      ],
    },
    repo: 'barryvdh/laravel-debugbar',
    docsDir: 'docs',
    editLink: true,
  }),
});
