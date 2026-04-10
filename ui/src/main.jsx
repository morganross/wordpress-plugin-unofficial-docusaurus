import React from 'react';
import { createRoot } from 'react-dom/client';
import './styles.css';

function SidebarEnhancer() {
  const [open, setOpen] = React.useState(false);
  const [query, setQuery] = React.useState('');

  React.useEffect(() => {
    document.documentElement.classList.toggle('udoc-nav-open', open);
    return () => document.documentElement.classList.remove('udoc-nav-open');
  }, [open]);

  React.useEffect(() => {
    const shell = document.querySelector('[data-udoc-shell="true"]');
    const sidebar = document.querySelector('.udoc-sidebar');
    if (!shell || !sidebar) return;

    const existing = document.querySelector('.udoc-toolbar');
    if (existing) return;

    const mount = document.createElement('div');
    mount.className = 'udoc-toolbar-mount';
    shell.prepend(mount);

    const filter = () => {
      const links = sidebar.querySelectorAll('a');
      const needle = query.trim().toLowerCase();
      links.forEach((link) => {
        const item = link.closest('li');
        if (!item) return;
        const visible = !needle || link.textContent.toLowerCase().includes(needle);
        item.style.display = visible ? '' : 'none';
      });
    };

    filter();

    return () => {
      mount.remove();
    };
  }, [query]);

  React.useEffect(() => {
    const sidebar = document.querySelector('.udoc-sidebar');
    if (!sidebar) return;
    const links = sidebar.querySelectorAll('a');
    const needle = query.trim().toLowerCase();
    links.forEach((link) => {
      const item = link.closest('li');
      if (!item) return;
      const visible = !needle || link.textContent.toLowerCase().includes(needle);
      item.style.display = visible ? '' : 'none';
    });
  }, [query]);

  return (
    <div className="udoc-toolbar">
      <button className="udoc-menu-button" type="button" onClick={() => setOpen((v) => !v)}>
        {open ? 'Close menu' : 'Open menu'}
      </button>
      <label className="udoc-search">
        <span className="screen-reader-text">Search docs</span>
        <input
          type="search"
          placeholder="Search docs"
          value={query}
          onChange={(event) => setQuery(event.target.value)}
        />
      </label>
    </div>
  );
}

const rootNode = document.getElementById('udoc-root');

if (rootNode) {
  const mount = document.createElement('div');
  rootNode.prepend(mount);
  createRoot(mount).render(<SidebarEnhancer />);
}
