/**
 * 依赖数据的统计汇总
 */

/**
 * @param {{ nodes: Array, edges: Array }} data
 */
export function computeStats(data) {
  const { nodes, edges } = data;
  const byId = new Map(nodes.map((n) => [n.id, n]));

  const out = new Map(nodes.map((n) => [n.id, []]));
  const inc = new Map(nodes.map((n) => [n.id, []]));
  for (const e of edges) {
    if (out.has(e.source)) out.get(e.source).push(e.target);
    if (inc.has(e.target)) inc.get(e.target).push(e.source);
  }

  const licenseDist = {};
  const typeCount = { library: 0, extension: 0 };
  const versionSourceDist = {};
  for (const n of nodes) {
    licenseDist[n.licenseName] = (licenseDist[n.licenseName] || 0) + 1;
    typeCount[n.type] = (typeCount[n.type] || 0) + 1;
    const key = n.versionSource || 'unknown';
    versionSourceDist[key] = (versionSourceDist[key] || 0) + 1;
  }

  const edgeKinds = {};
  for (const e of edges) edgeKinds[e.kind] = (edgeKinds[e.kind] || 0) + 1;

  const noVersion = nodes.filter((n) => !n.version);
  const noLicense = nodes.filter((n) => n.licenseName === 'Unknown');
  const inferredLicense = nodes.filter((n) => n.licenseInferred);
  const isolated = nodes.filter((n) => out.get(n.id).length + inc.get(n.id).length === 0);

  // 被依赖最多的节点（依赖图里的关键枢纽）
  const mostDepended = nodes
    .map((n) => ({ id: n.id, name: n.name, type: n.type, count: inc.get(n.id).length }))
    .sort((a, b) => b.count - a.count)
    .slice(0, 10);

  const sortObj = (obj) => Object.fromEntries(Object.entries(obj).sort((a, b) => b[1] - a[1]));

  return {
    total: nodes.length,
    typeCount,
    edgeTotal: edges.length,
    edgeKinds: sortObj(edgeKinds),
    licenseDist: sortObj(licenseDist),
    versionSourceDist: sortObj(versionSourceDist),
    noVersion: noVersion.map((n) => n.id),
    noLicense: noLicense.map((n) => n.id),
    inferredLicense: inferredLicense.length,
    isolated: isolated.map((n) => n.id),
    mostDepended,
    byId,
    out,
    inc,
  };
}

/** 把统计结果格式化为终端可读文本 */
export function formatStats(data, stats) {
  const lines = [];
  const m = data.meta;
  lines.push('=========================== 依赖数据校验 ===========================');
  lines.push(
    `PHP ${m.phpVersion} / swoole ${m.swooleVersion} / ${m.osType} / ${m.buildType}`
  );
  lines.push(`生成时间: ${m.generatedAt}`);
  lines.push('');
  lines.push(`库        : ${stats.typeCount.library}`);
  lines.push(`扩展      : ${stats.typeCount.extension}`);
  lines.push(`节点合计  : ${stats.total}`);
  lines.push(
    `依赖边    : ${stats.edgeTotal}  ` +
      Object.entries(stats.edgeKinds)
        .map(([k, v]) => `${k}=${v}`)
        .join('  ')
  );
  lines.push('');
  lines.push('许可证分布:');
  for (const [name, count] of Object.entries(stats.licenseDist)) {
    const pct = ((count / stats.total) * 100).toFixed(1);
    lines.push(`  ${name.padEnd(14)} ${String(count).padStart(3)}  ${pct}%`);
  }
  lines.push('');
  lines.push('版本来源:');
  for (const [name, count] of Object.entries(stats.versionSourceDist)) {
    lines.push(`  ${name.padEnd(14)} ${count}`);
  }
  lines.push('');
  lines.push(`未声明许可证: ${stats.noLicense.length ? stats.noLicense.join(', ') : '无'}`);
  lines.push(`缺少版本    : ${stats.noVersion.length ? stats.noVersion.join(', ') : '无'}`);
  lines.push(`推断许可证  : ${stats.inferredLicense} 个（php-src 内置扩展）`);
  lines.push(
    `无依赖边节点: ${stats.isolated.length ? stats.isolated.join(', ') : '无'}${
      stats.isolated.length ? '（多为不依赖第三方库的内置扩展）' : ''
    }`
  );
  lines.push('');
  lines.push('被依赖最多:');
  for (const item of stats.mostDepended) {
    if (item.count === 0) break;
    lines.push(`  ${item.id.padEnd(16)} ${item.count}`);
  }
  return lines.join('\n');
}
