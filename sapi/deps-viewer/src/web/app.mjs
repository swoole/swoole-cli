/**
 * 依赖图前端交互
 *
 * 说明：
 * - 本文件与 layout.mjs 一起被内联进单文件 HTML（由 src/render.mjs 拼接），
 *   构建时会剥掉 import/export，因此不要在此引入任何外部依赖
 * - 必须能在 file:// 下直接打开：不使用 fetch、不使用 ES module
 */
import { createSimulation, forceLayout } from './layout.mjs';

const NS = 'http://www.w3.org/2000/svg';
const VIEW_W = 1500;
const VIEW_H = 950;

const data = JSON.parse(document.getElementById('deps-data').textContent);
const nodes = data.nodes.map((n) => ({ ...n }));
const edges = data.edges.map((e) => ({ ...e }));

const byId = new Map(nodes.map((n) => [n.id, n]));
const outEdges = new Map(); // id -> 依赖的其它节点
const inEdges = new Map(); // id -> 依赖它的其它节点
for (const n of nodes) {
  outEdges.set(n.id, []);
  inEdges.set(n.id, []);
}
for (const e of edges) {
  if (outEdges.has(e.source)) outEdges.get(e.source).push(e.target);
  if (inEdges.has(e.target)) inEdges.get(e.target).push(e.source);
}

const state = {
  selected: null,
  typeFilter: new Set(['library', 'extension']),
  licenseFilter: new Set(), // 空集表示全部
  edgeFilter: new Set(['lib-lib', 'ext-lib', 'ext-ext']),
  searchHits: new Set(),
  vb: { x: 0, y: 0, w: VIEW_W, h: VIEW_H },
};

const el = {
  svg: document.getElementById('svg'),
  viewport: document.getElementById('viewport'),
  gNodes: document.getElementById('nodes'),
  gEdges: document.getElementById('edges'),
  tooltip: document.getElementById('tooltip'),
  stats: document.getElementById('stats'),
  versions: document.getElementById('versions'),
  detail: document.getElementById('detail'),
  search: document.getElementById('search'),
  suggest: document.getElementById('suggest'),
  themeBtn: document.getElementById('theme-btn'),
  filterType: document.getElementById('filter-type'),
  filterLicense: document.getElementById('filter-license'),
  filterEdge: document.getElementById('filter-edge'),
  issues: document.getElementById('issues'),
};

/* ---------------- 统计 ---------------- */

function renderVersions() {
  const m = data.meta;
  const items = [
    ['PHP', m.phpVersion],
    ['swoole', m.swooleVersion],
    [m.osType, m.buildType],
  ];
  el.versions.innerHTML = items
    .map(([k, v]) => `<span class="badge license-Custom">${k} ${v}</span>`)
    .join('');
}

function licenseColor(name) {
  const map = {
    MIT: '#3fb950',
    BSD: '#4fa3ff',
    'Apache-2.0': '#56c7c7',
    GPL: '#f0883e',
    LGPL: '#dbab0a',
    'PHP License': '#a371f7',
    Custom: '#6e7d8c',
    Unknown: '#57606a',
  };
  return map[name] || '#6e7d8c';
}

/** 许可证名 -> CSS 徽章类，必须与 app.css 里的 .badge.license-* 一一对应 */
const LICENSE_BADGE = {
  MIT: 'license-MIT',
  BSD: 'license-BSD',
  'Apache-2.0': 'license-Apache',
  GPL: 'license-GPL',
  LGPL: 'license-LGPL',
  'PHP License': 'license-PHP',
  Custom: 'license-Custom',
  Unknown: 'license-Unknown',
};

function licenseBadgeClass(name) {
  return LICENSE_BADGE[name] || 'license-Custom';
}

function renderStats() {
  const libs = nodes.filter((n) => n.type === 'library').length;
  const exts = nodes.length - libs;
  const dist = {};
  for (const n of nodes) dist[n.licenseName] = (dist[n.licenseName] || 0) + 1;
  const entries = Object.entries(dist).sort((a, b) => b[1] - a[1]);

  const bars = entries
    .map(
      ([name, count]) =>
        `<span style="width:${(count / nodes.length) * 100}%;background:${licenseColor(name)}" title="${name}: ${count}"></span>`
    )
    .join('');
  const legend = entries
    .map(
      ([name, count]) =>
        `<span><span class="dot" style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${licenseColor(name)};margin-right:4px"></span>${name} ${count}</span>`
    )
    .join('');

  el.stats.innerHTML = `
    <div class="stat"><div class="value">${libs}</div><div class="label">第三方库</div></div>
    <div class="stat"><div class="value">${exts}</div><div class="label">PHP 扩展</div></div>
    <div class="stat"><div class="value">${edges.length}</div><div class="label">依赖边</div></div>
    <div class="stat wide">
      <div class="label" style="margin-bottom:2px">许可证分布</div>
      <div class="license-bars">${bars}</div>
      <div class="license-legend">${legend}</div>
    </div>`;
}

function renderIssues() {
  const noLicense = nodes.filter((n) => n.licenseName === 'Unknown');
  const noVersion = nodes.filter((n) => !n.version);
  const inferred = nodes.filter((n) => n.licenseInferred);
  const isolated = nodes.filter((n) => outEdges.get(n.id).length + inEdges.get(n.id).length === 0);

  const items = [];
  if (noLicense.length)
    items.push(`<li>未声明许可证 <b>${noLicense.length}</b> 个：<code>${noLicense.map((n) => n.name).join('、')}</code></li>`);
  if (noVersion.length)
    items.push(`<li>缺少版本 <b>${noVersion.length}</b> 个</li>`);
  if (inferred.length)
    items.push(
      `<li>${inferred.length} 个内置扩展的许可证为按 php-src 推断，配置中未显式声明</li>`
    );
  if (isolated.length)
    items.push(
      `<li>无依赖边的节点 <b>${isolated.length}</b> 个（多为不依赖第三方库的内置扩展）：<code>${isolated.map((n) => n.name).join('、')}</code></li>`
    );

  el.issues.innerHTML = items.length
    ? `<ul class="issues">${items.join('')}</ul>`
    : '<div style="color:var(--text-faint);font-size:12.5px">无异常</div>';
}

/* ---------------- 筛选栏 ---------------- */

function renderFilters() {
  const libs = nodes.filter((n) => n.type === 'library').length;
  el.filterType.innerHTML = [
    ['library', '第三方库', libs, 'var(--lib)'],
    ['extension', 'PHP 扩展', nodes.length - libs, 'var(--ext)'],
  ]
    .map(
      ([key, label, count, color]) =>
        `<label class="filter-row"><input type="checkbox" data-type="${key}" checked>
         <span class="dot" style="background:${color}"></span>${label}
         <span class="count">${count}</span></label>`
    )
    .join('');

  const dist = {};
  for (const n of nodes) dist[n.licenseName] = (dist[n.licenseName] || 0) + 1;
  el.filterLicense.innerHTML = Object.entries(dist)
    .sort((a, b) => b[1] - a[1])
    .map(
      ([name, count]) =>
        `<label class="filter-row"><input type="checkbox" data-license="${name}">
         <span class="dot" style="background:${licenseColor(name)}"></span>${name}
         <span class="count">${count}</span></label>`
    )
    .join('');

  const kinds = {};
  for (const e of edges) kinds[e.kind] = (kinds[e.kind] || 0) + 1;
  const labels = { 'lib-lib': '库 → 库', 'ext-lib': '扩展 → 库', 'ext-ext': '扩展 → 扩展' };
  el.filterEdge.innerHTML = Object.entries(labels)
    .map(
      ([key, label]) =>
        `<label class="filter-row"><input type="checkbox" data-edge="${key}" checked>
         ${label}<span class="count">${kinds[key] || 0}</span></label>`
    )
    .join('');

  el.filterType.addEventListener('change', (e) => {
    const t = e.target.dataset.type;
    if (!t) return;
    if (e.target.checked) state.typeFilter.add(t);
    else state.typeFilter.delete(t);
    applyFilters();
  });
  el.filterLicense.addEventListener('change', (e) => {
    const l = e.target.dataset.license;
    if (!l) return;
    if (e.target.checked) state.licenseFilter.add(l);
    else state.licenseFilter.delete(l);
    applyFilters();
  });
  el.filterEdge.addEventListener('change', (e) => {
    const k = e.target.dataset.edge;
    if (!k) return;
    if (e.target.checked) state.edgeFilter.add(k);
    else state.edgeFilter.delete(k);
    applyFilters();
  });
}

/* ---------------- 图渲染 ---------------- */

const nodeEls = new Map();
const edgeEls = [];

function renderGraph() {
  el.gEdges.innerHTML = '';
  el.gNodes.innerHTML = '';
  nodeEls.clear();
  edgeEls.length = 0;

  for (const e of edges) {
    const path = document.createElementNS(NS, 'path');
    path.setAttribute('class', 'edge ' + e.kind);
    path.setAttribute('marker-end', 'url(#arrow)');
    path.dataset.source = e.source;
    path.dataset.target = e.target;
    path.dataset.kind = e.kind;
    el.gEdges.appendChild(path);
    edgeEls.push({ edge: e, el: path });
  }

  for (const n of nodes) {
    const deg = outEdges.get(n.id).length + inEdges.get(n.id).length;
    const g = document.createElementNS(NS, 'g');
    g.setAttribute('class', 'node ' + (n.type === 'library' ? 'lib' : 'ext'));
    g.dataset.id = n.id;

    const circle = document.createElementNS(NS, 'circle');
    circle.setAttribute('r', String(5.5 + Math.min(deg, 10) * 0.42));
    g.appendChild(circle);

    const text = document.createElementNS(NS, 'text');
    text.setAttribute('text-anchor', 'middle');
    text.textContent = n.name;
    g.appendChild(text);

    el.gNodes.appendChild(g);
    nodeEls.set(n.id, { node: n, g, circle, text, r: parseFloat(circle.getAttribute('r')) });
  }
}

function updatePositions() {
  for (const { node, g, text, r } of nodeEls.values()) {
    g.setAttribute('transform', `translate(${node.x.toFixed(2)},${node.y.toFixed(2)})`);
    text.setAttribute('y', (-r - 4).toFixed(2));
  }
  for (const { edge, el: path } of edgeEls) {
    const a = byId.get(edge.source);
    const b = byId.get(edge.target);
    if (!a || !b) continue;
    const dx = b.x - a.x;
    const dy = b.y - a.y;
    const d = Math.hypot(dx, dy) || 1;
    const ra = (nodeEls.get(edge.source)?.r ?? 6) + 1;
    const rb = (nodeEls.get(edge.target)?.r ?? 6) + 4;
    const x1 = a.x + (dx / d) * ra;
    const y1 = a.y + (dy / d) * ra;
    const x2 = b.x - (dx / d) * rb;
    const y2 = b.y - (dy / d) * rb;
    // 轻微弧线，避免双向边完全重合
    const mx = (x1 + x2) / 2 - dy * 0.06;
    const my = (y1 + y2) / 2 + dx * 0.06;
    path.setAttribute('d', `M${x1.toFixed(1)},${y1.toFixed(1)} Q${mx.toFixed(1)},${my.toFixed(1)} ${x2.toFixed(1)},${y2.toFixed(1)}`);
  }
}

/* ---------------- 视图变换 ---------------- */

function applyView() {
  el.svg.setAttribute('viewBox', `${state.vb.x} ${state.vb.y} ${state.vb.w} ${state.vb.h}`);
}

function svgPoint(clientX, clientY) {
  const rect = el.svg.getBoundingClientRect();
  const scale = Math.min(rect.width / state.vb.w, rect.height / state.vb.h) || 1;
  const offX = (rect.width - state.vb.w * scale) / 2;
  const offY = (rect.height - state.vb.h * scale) / 2;
  return {
    x: (clientX - rect.left - offX) / scale + state.vb.x,
    y: (clientY - rect.top - offY) / scale + state.vb.y,
  };
}

function zoomAt(factor, cx, cy) {
  const w2 = Math.max(180, Math.min(VIEW_W * 2.5, state.vb.w * factor));
  const k = w2 / state.vb.w;
  state.vb.x = cx - (cx - state.vb.x) * k;
  state.vb.y = cy - (cy - state.vb.y) * k;
  state.vb.w = w2;
  state.vb.h = state.vb.h * k;
  applyView();
}

function centerOn(node, scale) {
  if (scale) {
    state.vb.w = VIEW_W * scale;
    state.vb.h = VIEW_H * scale;
  }
  state.vb.x = node.x - state.vb.w / 2;
  state.vb.y = node.y - state.vb.h / 2;
  applyView();
}

/* ---------------- 选中与高亮 ---------------- */

function related(id) {
  const down = new Set(); // 它依赖的
  const up = new Set(); // 依赖它的
  const walk = (start, map, acc) => {
    const queue = [start];
    while (queue.length) {
      const cur = queue.pop();
      for (const next of map.get(cur) || []) {
        if (acc.has(next)) continue;
        acc.add(next);
        queue.push(next);
      }
    }
  };
  walk(id, outEdges, down);
  walk(id, inEdges, up);
  return { up, down };
}

function select(id, opts = {}) {
  state.selected = id;
  const { up, down } = related(id);

  for (const [nid, item] of nodeEls) {
    item.g.classList.remove('dim', 'up', 'down', 'selected', 'hit');
    if (nid === id) {
      item.g.classList.add('selected');
      continue;
    }
    if (down.has(nid)) item.g.classList.add('down');
    else if (up.has(nid)) item.g.classList.add('up');
    else item.g.classList.add('dim');
  }
  for (const { edge, el: path } of edgeEls) {
    path.classList.remove('dim', 'highlight-up', 'highlight-down');
    path.setAttribute('marker-end', 'url(#arrow)');
    const touchesSelected = edge.source === id || edge.target === id;
    const inUp = up.has(edge.source) && up.has(edge.target);
    const inDown = down.has(edge.source) && down.has(edge.target);
    if (touchesSelected || inUp || inDown) {
      const isDown = down.has(edge.target) || (edge.source === id && down.has(edge.target));
      if (edge.target === id || (up.has(edge.source) && up.has(edge.target))) {
        path.classList.add('highlight-up');
        path.setAttribute('marker-end', 'url(#arrow-up)');
      } else {
        path.classList.add('highlight-down');
        path.setAttribute('marker-end', 'url(#arrow-down)');
      }
      void isDown;
    } else {
      path.classList.add('dim');
    }
  }

  renderDetail(id);
  if (opts.center) centerOn(byId.get(id), 0.55);
}

function clearSelection() {
  state.selected = null;
  for (const item of nodeEls.values()) {
    item.g.classList.remove('dim', 'up', 'down', 'selected', 'hit');
  }
  for (const { el: path } of edgeEls) {
    path.classList.remove('dim', 'highlight-up', 'highlight-down');
    path.setAttribute('marker-end', 'url(#arrow)');
  }
  el.detail.innerHTML = '<div class="empty">点击左侧图中任一节点查看详情</div>';
  applyFilters();
}

/* ---------------- 详情面板 ---------------- */

const VERSION_SOURCE_LABEL = {
  'source-file': '源码包文件名',
  pecl: 'PECL 版本',
  swoole: 'SWOOLE-VERSION.conf',
  php: 'PHP 源码版本（内置扩展无独立版本）',
  '': '未知',
};

function renderDetail(id) {
  const n = byId.get(id);
  if (!n) return;
  const outs = outEdges.get(id) || [];
  const ins = inEdges.get(id) || [];
  const kindLabel = n.type === 'library' ? '第三方库' : 'PHP 扩展';
  const badgeCls = n.type === 'library' ? 'lib' : 'ext';

  const depsHtml = outs.length
    ? outs.map((d) => `<button data-goto="${d}">${byId.get(d)?.name ?? d}</button>`).join('')
    : '<span class="none">无</span>';
  const rdepsHtml = ins.length
    ? ins.map((d) => `<button data-goto="${d}">${byId.get(d)?.name ?? d}</button>`).join('')
    : '<span class="none">无</span>';

  const inferredTip = n.licenseInferred
    ? '<span class="badge license-Unknown inferred" title="配置中未声明，按 php-src 内置扩展推断为 PHP License">推断</span>'
    : '';

  el.detail.innerHTML = `
    <div class="detail-title">${n.name}
      <span class="badge ${badgeCls}">${kindLabel}</span>
    </div>
    <div class="detail-sub">${n.id}</div>
    <dl class="kv">
      <dt>版本</dt><dd>${n.version || '<span style="color:var(--text-faint)">未知</span>'}</dd>
      <dt>版本来源</dt><dd style="color:var(--text-dim)">${VERSION_SOURCE_LABEL[n.versionSource] ?? n.versionSource}</dd>
      <dt>许可证</dt><dd>
        <span class="badge ${licenseBadgeClass(n.licenseName)}">${n.licenseName}</span>${inferredTip}
        ${n.licenseUrl ? `<div style="margin-top:4px"><a href="${n.licenseUrl}" target="_blank" rel="noopener">许可证全文 ↗</a></div>` : ''}
      </dd>
      ${
        n.homePage
          ? `<dt>主页</dt><dd><a href="${n.homePage}" target="_blank" rel="noopener">${n.homePage} ↗</a></dd>`
          : ''
      }
      ${n.manual ? `<dt>文档</dt><dd><a href="${n.manual}" target="_blank" rel="noopener">${n.manual} ↗</a></dd>` : ''}
      ${n.sourceFile ? `<dt>源码包</dt><dd style="font-family:var(--mono);font-size:12px">${n.sourceFile}</dd>` : ''}
      ${n.options ? `<dt>编译选项</dt><dd style="font-family:var(--mono);font-size:12px">${n.options}</dd>` : ''}
    </dl>
    <h3>依赖 (${outs.length})</h3>
    <div class="deps-list">${depsHtml}</div>
    <h3 style="margin-top:16px">被依赖 (${ins.length})</h3>
    <div class="deps-list">${rdepsHtml}</div>`;

  el.detail.querySelectorAll('[data-goto]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const target = btn.dataset.goto;
      if (byId.has(target)) select(target, { center: true });
    });
  });
}

/* ---------------- 筛选 ---------------- */

function nodeVisible(n) {
  if (!state.typeFilter.has(n.type)) return false;
  if (state.licenseFilter.size && !state.licenseFilter.has(n.licenseName)) return false;
  return true;
}

function applyFilters() {
  const visible = new Set();
  for (const n of nodes) {
    const ok = nodeVisible(n);
    const item = nodeEls.get(n.id);
    if (item) item.g.style.display = ok ? '' : 'none';
    if (ok) visible.add(n.id);
  }
  for (const { edge, el: path } of edgeEls) {
    const ok =
      state.edgeFilter.has(edge.kind) && visible.has(edge.source) && visible.has(edge.target);
    path.style.display = ok ? '' : 'none';
  }
}

/* ---------------- 搜索 ---------------- */

function renderSuggest(q) {
  const query = q.trim().toLowerCase();
  if (!query) {
    el.suggest.classList.remove('open');
    el.suggest.innerHTML = '';
    state.searchHits.clear();
    for (const item of nodeEls.values()) item.g.classList.remove('hit');
    return;
  }
  const hits = nodes
    .filter((n) => n.name.toLowerCase().includes(query) || n.id.toLowerCase().includes(query))
    .slice(0, 12);
  state.searchHits = new Set(hits.map((n) => n.id));

  el.suggest.innerHTML = hits
    .map(
      (n) =>
        `<button data-goto="${n.id}"><span class="dot" style="background:${
          n.type === 'library' ? 'var(--lib)' : 'var(--ext)'
        }"></span><span style="font-family:var(--mono)">${n.name}</span>
         <span style="margin-left:auto;color:var(--text-faint);font-size:11px">${n.version || ''} · ${n.licenseName}</span></button>`
    )
    .join('');
  el.suggest.classList.toggle('open', hits.length > 0);

  for (const item of nodeEls.values()) {
    item.g.classList.toggle('hit', state.searchHits.has(item.node.id));
  }
  el.suggest.querySelectorAll('[data-goto]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.goto;
      el.suggest.classList.remove('open');
      el.search.value = byId.get(id).name;
      select(id, { center: true });
    });
  });
}

/* ---------------- 交互绑定 ---------------- */

let simulation = null;

function bindGraph() {
  el.svg.setAttribute('viewBox', `0 0 ${VIEW_W} ${VIEW_H}`);

  // 背景拖拽平移
  let panning = null;
  el.svg.addEventListener('pointerdown', (e) => {
    if (e.target.closest('.node')) return;
    panning = { x: e.clientX, y: e.clientY, vx: state.vb.x, vy: state.vb.y };
    el.svg.classList.add('dragging');
    el.svg.setPointerCapture(e.pointerId);
  });
  el.svg.addEventListener('pointermove', (e) => {
    if (panning) {
      const rect = el.svg.getBoundingClientRect();
      const scale = Math.min(rect.width / state.vb.w, rect.height / state.vb.h) || 1;
      state.vb.x = panning.vx - (e.clientX - panning.x) / scale;
      state.vb.y = panning.vy - (e.clientY - panning.y) / scale;
      applyView();
    }
  });
  const endPan = () => {
    panning = null;
    el.svg.classList.remove('dragging');
  };
  el.svg.addEventListener('pointerup', endPan);
  el.svg.addEventListener('pointercancel', endPan);

  // 滚轮缩放
  el.svg.addEventListener(
    'wheel',
    (e) => {
      e.preventDefault();
      const p = svgPoint(e.clientX, e.clientY);
      zoomAt(e.deltaY < 0 ? 1 / 1.13 : 1.13, p.x, p.y);
    },
    { passive: false }
  );

  // 节点拖拽 / 点击
  for (const item of nodeEls.values()) {
    let drag = null;
    item.g.addEventListener('pointerdown', (e) => {
      e.stopPropagation();
      const p = svgPoint(e.clientX, e.clientY);
      drag = { id: item.node.id, moved: false, startX: e.clientX, startY: e.clientY };
      item.node.fixed = true;
      item.node.fx = p.x;
      item.node.fy = p.y;
      item.g.setPointerCapture(e.pointerId);
    });
    item.g.addEventListener('pointermove', (e) => {
      if (!drag) return;
      if (Math.hypot(e.clientX - drag.startX, e.clientY - drag.startY) > 3) drag.moved = true;
      const p = svgPoint(e.clientX, e.clientY);
      item.node.fx = p.x;
      item.node.fy = p.y;
      item.node.x = p.x;
      item.node.y = p.y;
      if (drag.moved && simulation) {
        simulation.tick(3);
        updatePositions();
      }
    });
    const endDrag = () => {
      if (!drag) return;
      const { id, moved } = drag;
      drag = null;
      item.node.fixed = false;
      if (moved) {
        if (simulation) {
          simulation.tick(40);
          updatePositions();
        }
      } else {
        select(id);
      }
    };
    item.g.addEventListener('pointerup', endDrag);
    item.g.addEventListener('pointercancel', endDrag);

    // tooltip
    item.g.addEventListener('pointerenter', (e) => {
      if (drag) return;
      const n = item.node;
      el.tooltip.innerHTML = `<div class="t-name">${n.name}</div>
        <div class="t-meta">${n.type === 'library' ? '库' : '扩展'} · ${n.version || '版本未知'} · ${n.licenseName}</div>`;
      el.tooltip.classList.add('open');
      moveTooltip(e);
    });
    item.g.addEventListener('pointermove', moveTooltip);
    item.g.addEventListener('pointerleave', () => el.tooltip.classList.remove('open'));
  }

  function moveTooltip(e) {
    const rect = el.svg.parentElement.getBoundingClientRect();
    let x = e.clientX - rect.left + 14;
    let y = e.clientY - rect.top + 14;
    if (x + 270 > rect.width) x -= 285;
    el.tooltip.style.left = x + 'px';
    el.tooltip.style.top = y + 'px';
  }

  // 点击空白取消选中
  el.svg.addEventListener('click', (e) => {
    if (!e.target.closest('.node')) clearSelection();
  });

  document.getElementById('zoom-in').addEventListener('click', () => zoomAt(1 / 1.25, state.vb.x + state.vb.w / 2, state.vb.y + state.vb.h / 2));
  document.getElementById('zoom-out').addEventListener('click', () => zoomAt(1.25, state.vb.x + state.vb.w / 2, state.vb.y + state.vb.h / 2));
  document.getElementById('zoom-reset').addEventListener('click', () => {
    state.vb = { x: 0, y: 0, w: VIEW_W, h: VIEW_H };
    applyView();
  });
  document.getElementById('relayout').addEventListener('click', () => {
    forceLayout(nodes, edges);
    updatePositions();
    if (state.selected) select(state.selected);
  });

  el.search.addEventListener('input', (e) => renderSuggest(e.target.value));
  el.search.addEventListener('blur', () => setTimeout(() => el.suggest.classList.remove('open'), 150));

  el.themeBtn.addEventListener('click', () => {
    const next = document.body.dataset.theme === 'dark' ? 'light' : 'dark';
    document.body.dataset.theme = next;
    document.documentElement.dataset.theme = next;
    el.themeBtn.textContent = next === 'dark' ? '亮色' : '暗色';
  });
}

/* ---------------- 启动 ---------------- */

renderVersions();
renderStats();
renderIssues();
renderFilters();
renderGraph();

simulation = createSimulation(nodes, edges, { width: VIEW_W, height: VIEW_H });
simulation.run();
updatePositions();
applyFilters();
bindGraph();
