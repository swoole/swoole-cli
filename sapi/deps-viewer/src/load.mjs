import { readFileSync } from 'node:fs';

/**
 * 读取并校验 PHP 侧导出的依赖 JSON
 *
 * @param {string} file JSON 路径
 * @returns {{ data: object, warnings: string[] }}
 */
export function loadDependencyJson(file) {
  let data;
  try {
    data = JSON.parse(readFileSync(file, 'utf8'));
  } catch (err) {
    throw new Error(`无法解析 ${file}: ${err.message}`);
  }

  const errors = [];
  const warnings = [];

  if (!data.meta) errors.push('缺少 meta 字段');
  if (!Array.isArray(data.nodes)) errors.push('缺少 nodes 数组');
  if (!Array.isArray(data.edges)) errors.push('缺少 edges 数组');
  if (errors.length) throw new Error(`JSON 结构不完整:\n  - ${errors.join('\n  - ')}`);

  const seen = new Set();
  for (const node of data.nodes) {
    if (!node.id || !node.type || !node.name) {
      errors.push(`节点字段不完整: ${JSON.stringify(node).slice(0, 80)}`);
    }
    if (seen.has(node.id)) errors.push(`节点 id 重复: ${node.id}`);
    seen.add(node.id);
    if (!['library', 'extension'].includes(node.type)) {
      errors.push(`未知节点类型: ${node.type} (${node.id})`);
    }
  }
  if (errors.length) throw new Error(`节点校验失败:\n  - ${errors.join('\n  - ')}`);

  let dangling = 0;
  for (const edge of data.edges) {
    if (!seen.has(edge.source)) {
      dangling++;
      warnings.push(`边 ${edge.source} -> ${edge.target} 的 source 不存在`);
    }
    if (!seen.has(edge.target)) {
      dangling++;
      warnings.push(`边 ${edge.source} -> ${edge.target} 的 target 不存在`);
    }
  }
  void dangling;

  // 缺失信息只作提示，不阻断构建
  const noVersion = data.nodes.filter((n) => !n.version);
  const noLicense = data.nodes.filter((n) => n.licenseName === 'Unknown');
  if (noVersion.length) {
    warnings.push(`${noVersion.length} 个节点缺少版本: ${noVersion.map((n) => n.id).join(', ')}`);
  }
  if (noLicense.length) {
    warnings.push(
      `${noLicense.length} 个节点未声明许可证: ${noLicense.map((n) => n.id).join(', ')}`
    );
  }

  return { data, warnings };
}
