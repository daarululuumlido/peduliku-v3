/** @type {import('@docusaurus/plugin-content-docs').SidebarsConfig} */
const sidebars = {
  docsSidebar: [
    {
      type: 'doc',
      id: 'intro',
      label: '📋 Blueprint',
    },
    {
      type: 'doc',
      id: 'tech-stack',
      label: '🛠️ Tech Stack',
    },
    {
      type: 'doc',
      id: 'design-decisions',
      label: '📐 Keputusan Desain',
    },
    {
      type: 'category',
      label: '📁 Fase Pengembangan',
      collapsed: false,
      items: [
        {
          type: 'doc',
          id: 'fase/fase-1-fondasi',
          label: 'Fase 1: Fondasi & Manajemen Orang',
        },
        {
          type: 'doc',
          id: 'fase/fase-2-hris',
          label: 'Fase 2: HRIS & Organisasi',
        },
        {
          type: 'doc',
          id: 'fase/fase-3-santri',
          label: 'Fase 3: Santri & Akademik',
        },
      ],
    },
  ],
};

export default sidebars;
