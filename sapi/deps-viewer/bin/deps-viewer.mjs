#!/usr/bin/env node
/**
 * swoole-cli 构建依赖可视化工具
 *
 * 用法：
 *   node bin/deps-viewer.mjs check   --json ../../bin/dependency-graph.json
 *   node bin/deps-viewer.mjs build   --json ../../bin/dependency-graph.json --out dist/index.html
 *   node bin/deps-viewer.mjs verify  --json ../../bin/dependency-graph.json
 *
 * 零第三方依赖，只用 Node 内置模块。
 */
import { resolve } from 'node:path';
import { existsSync, statSync } from 'node:fs';
import { loadDependencyJson } from '../src/load.mjs';
import { computeStats, formatStats } from '../src/stats.mjs';
import { renderHtml } from '../src/render.mjs';
import { createSimulation } from '../src/web/layout.mjs';

const args = process.argv.slice(2);
const command = args[0] || 'build';

function parseOptions(argv) {
  const opts = {};
  for (let i = 0; i < argv.length; i++) {
    const arg = argv[i];
    if (!arg.startsWith('--')) continue;
    const [key, inlineValue] = arg.slice(2).split('=');
    const value = inlineValue ?? argv[++i];
    opts[key] = value;
  }
  return opts;
}

const opts = parseOptions(args.slice(1));
const jsonFile = resolve(process.cwd(), opts.json || '../../bin/dependency-graph.json');
const outFile = resolve(process.cwd(), opts.out || 'dist/index.html');

function requireJson() {
  if (!existsSync(jsonFile)) {
    console.error(`找不到依赖数据: ${jsonFile}`);
    console.error('请先生成： php prepare.php --with-dependency-json=1');
    process.exit(1);
  }
  return jsonFile;
}

if (command === 'check') {
  const { data, warnings } = loadDependencyJson(requireJson());
  const stats = computeStats(data);
  console.log(formatStats(data, stats));
  if (warnings.length) {
    console.log('');
    console.log('警告:');
    for (const w of warnings) console.log(`  - ${w}`);
  } else {
    console.log('');
    console.log('数据校验通过，无警告。');
  }
} else if (command === 'build') {
  const { data, warnings } = loadDependencyJson(requireJson());
  const stats = computeStats(data);
  console.log(formatStats(data, stats));
  if (warnings.length) {
    console.log('');
    console.log('警告:');
    for (const w of warnings) console.log(`  - ${w}`);
  }

  const { outFile: out, bytes } = renderHtml(data, outFile);
  console.log('');
  console.log('=============================== 构建 ===============================');
  console.log(`产物: ${out}`);
  console.log(`大小: ${(bytes / 1024).toFixed(1)} KB`);
  console.log(`节点: ${stats.total}  边: ${stats.edgeTotal}`);
  console.log('');
  console.log('直接用浏览器打开该文件即可（无需服务器）。');
} else if (command === 'verify') {
  // 验证布局算法：无 NaN、有合理间距、耗时可控
  const { data } = loadDependencyJson(requireJson());
  const nodes = data.nodes.map((n) => ({ ...n }));

  const t0 = performance.now();
  const sim = createSimulation(nodes, data.edges, { width: 1500, height: 950 });
  sim.run();
  const ms = performance.now() - t0;

  const bad = nodes.filter((n) => !Number.isFinite(n.x) || !Number.isFinite(n.y));
  let minDist = Infinity;
  let minPair = '';
  for (let i = 0; i < nodes.length; i++) {
    for (let j = i + 1; j < nodes.length; j++) {
      const d = Math.hypot(nodes[i].x - nodes[j].x, nodes[i].y - nodes[j].y);
      if (d < minDist) {
        minDist = d;
        minPair = `${nodes[i].id} / ${nodes[j].id}`;
      }
    }
  }

  const libs = nodes.filter((n) => n.type === 'library');
  const exts = nodes.filter((n) => n.type === 'extension');
  const avg = (arr) => arr.reduce((s, n) => s + n.x, 0) / (arr.length || 1);

  // 布局确定性
  const again = data.nodes.map((n) => ({ ...n }));
  createSimulation(again, data.edges, { width: 1500, height: 950 }).run();
  const deterministic = nodes.every(
    (n, i) => Math.abs(n.x - again[i].x) < 1e-9 && Math.abs(n.y - again[i].y) < 1e-9
  );

  console.log('=========================== 布局算法验证 ===========================');
  console.log(`节点数        : ${nodes.length}`);
  console.log(`耗时          : ${ms.toFixed(1)} ms   (阈值 150 ms)`);
  console.log(`NaN 坐标      : ${bad.length}   (期望 0)`);
  console.log(`最小节点间距  : ${minDist.toFixed(1)} px  (${minPair})   (阈值 12 px)`);
  console.log(`布局确定性    : ${deterministic ? 'OK' : 'FAIL'}`);
  console.log(
    `分层效果      : 库平均 x=${avg(libs).toFixed(0)}  扩展平均 x=${avg(exts).toFixed(0)}  (期望 库 < 扩展)`
  );

  const ok = bad.length === 0 && minDist > 12 && ms < 150 && deterministic && avg(libs) < avg(exts);
  console.log('');
  console.log(ok ? '布局算法验证通过。' : '布局算法验证未通过，请检查上面的指标。');
  process.exit(ok ? 0 : 1);
} else {
  console.error(`未知命令: ${command}`);
  console.error('可用命令: check | build | verify');
  process.exit(1);
}

void statSync;
