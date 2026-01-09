// @ts-check
import { themes as prismThemes } from 'prism-react-renderer';

/** @type {import('@docusaurus/types').Config} */
const config = {
  title: 'PeduliKu v3',
  tagline: 'Sistem Manajemen Terpadu untuk Pesantren',
  favicon: 'img/favicon.ico',

  future: {
    v4: true,
  },

  // Ganti dengan URL deployment kamu nanti
  url: 'https://peduliku-docs.example.com',
  baseUrl: '/',

  // GitHub pages deployment config
  organizationName: 'daarululuumlido', // Ganti dengan GitHub org/user name
  projectName: 'peduliku-v3', // Ganti dengan repo name

  onBrokenLinks: 'warn',
  onBrokenMarkdownLinks: 'warn',

  i18n: {
    defaultLocale: 'id',
    locales: ['id'],
  },

  presets: [
    [
      'classic',
      /** @type {import('@docusaurus/preset-classic').Options} */
      ({
        docs: {
          sidebarPath: './sidebars.js',
          routeBasePath: '/', // Docs sebagai homepage
        },
        blog: false, // Disable blog
        theme: {
          customCss: './src/css/custom.css',
        },
      }),
    ],
  ],

  themeConfig:
    /** @type {import('@docusaurus/preset-classic').ThemeConfig} */
    ({
      image: 'img/peduliku-social-card.jpg',
      colorMode: {
        defaultMode: 'light',
        respectPrefersColorScheme: true,
      },
      navbar: {
        title: 'PeduliKu v3',
        logo: {
          alt: 'PeduliKu Logo',
          src: 'img/logo.svg',
        },
        items: [
          {
            type: 'docSidebar',
            sidebarId: 'docsSidebar',
            position: 'left',
            label: 'Dokumentasi',
          },
          {
            href: 'https://github.com/daarululuumlido/peduliku-v3',
            label: 'GitHub',
            position: 'right',
          },
        ],
      },
      footer: {
        style: 'dark',
        links: [
          {
            title: 'Dokumentasi',
            items: [
              {
                label: 'Blueprint',
                to: '/',
              },
              {
                label: 'Tech Stack',
                to: '/tech-stack',
              },
              {
                label: 'Design Decisions',
                to: '/design-decisions',
              },
            ],
          },
          {
            title: 'Fase Pengembangan',
            items: [
              {
                label: 'Fase 1: Fondasi',
                to: '/fase/fase-1-fondasi',
              },
              {
                label: 'Fase 2: HRIS',
                to: '/fase/fase-2-hris',
              },
              {
                label: 'Fase 3: Santri',
                to: '/fase/fase-3-santri',
              },
            ],
          },
        ],
        copyright: `Copyright © ${new Date().getFullYear()} PeduliKu. Built with Docusaurus.`,
      },
      prism: {
        theme: prismThemes.github,
        darkTheme: prismThemes.dracula,
        additionalLanguages: ['php', 'bash'],
      },
    }),
};

export default config;
