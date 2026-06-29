/*
 * SDP Cinema docs viewer.
 * Single-page Markdown reader: loads ./content/<slug>.md, renders with marked,
 * promotes ```mermaid fences to <div class="mermaid"> so Mermaid draws the ERD,
 * seat state machine and booking flow, and highlights other code blocks.
 * Hash routing (#/<slug>) keeps every page deep-linkable.
 */

// Section order + labels. Files are the rendered mirror of artifacts/ai-context.
const PAGES = [
  { slug: 'overview',       file: '01-overview.md',        title: 'Project Overview' },
  { slug: 'architecture',   file: '02-architecture.md',    title: 'Architecture & Diagrams' },
  { slug: 'ui',             file: '03-ui.md',              title: 'UI Context' },
  { slug: 'code-standards', file: '04-code-standards.md',  title: 'Code Standards' },
  { slug: 'workflow',       file: '05-workflow.md',        title: 'AI Workflow Rules' },
];

mermaid.initialize({
  startOnLoad: false,
  theme: 'dark',
  securityLevel: 'loose',
  themeVariables: {
    background: '#0B0B0F',
    primaryColor: '#16161C',
    primaryTextColor: '#FFFFFF',
    primaryBorderColor: '#2A2A33',
    lineColor: '#9A9AA5',
    fontFamily: 'system-ui, sans-serif',
  },
});

// Render ```mermaid blocks as Mermaid divs; everything else stays a code block.
const renderer = new marked.Renderer();
const baseCode = renderer.code.bind(renderer);
renderer.code = function (code, lang) {
  if (lang === 'mermaid') {
    return `<div class="mermaid">${code}</div>`;
  }
  return baseCode(code, lang);
};
marked.setOptions({ renderer, gfm: true, breaks: false });

function buildSidebar() {
  const nav = document.getElementById('sidebar');
  nav.innerHTML = '';
  PAGES.forEach((p) => {
    const a = document.createElement('a');
    a.href = `#/${p.slug}`;
    a.textContent = p.title;
    a.dataset.slug = p.slug;
    a.className = 'nav-link';
    nav.appendChild(a);
  });
}

function markActive(slug) {
  document.querySelectorAll('.nav-link').forEach((a) => {
    a.classList.toggle('active', a.dataset.slug === slug);
  });
}

async function loadPage(slug) {
  const page = PAGES.find((p) => p.slug === slug) || PAGES[0];
  const content = document.getElementById('content');
  markActive(page.slug);
  content.innerHTML = '<p class="loading">Loading&hellip;</p>';
  try {
    const res = await fetch(`./content/${page.file}`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const md = await res.text();
    content.innerHTML = marked.parse(md);

    // Draw any Mermaid diagrams on the page.
    const diagrams = content.querySelectorAll('.mermaid');
    if (diagrams.length) {
      await mermaid.run({ nodes: diagrams });
    }
    // Syntax-highlight non-mermaid code blocks.
    content.querySelectorAll('pre code').forEach((block) => {
      if (!block.closest('.mermaid')) hljs.highlightElement(block);
    });
    content.scrollTop = 0;
    window.scrollTo(0, 0);
  } catch (err) {
    content.innerHTML =
      `<div class="error"><h2>Could not load this page</h2><p><code>${page.file}</code> &mdash; ${err.message}.</p>` +
      `<p>This site reads local Markdown over <code>fetch()</code>, which the browser blocks on <code>file://</code>. ` +
      `Serve it over HTTP instead &mdash; see <code>documents/README.md</code> (e.g. <code>npx serve documents</code>).</p></div>`;
  }
}

function route() {
  const slug = (location.hash.replace(/^#\//, '') || PAGES[0].slug).trim();
  loadPage(slug);
  document.getElementById('sidebar').classList.remove('open');
}

window.addEventListener('hashchange', route);
window.addEventListener('DOMContentLoaded', () => {
  buildSidebar();
  document.getElementById('menu-toggle').addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('open');
  });
  route();
});
