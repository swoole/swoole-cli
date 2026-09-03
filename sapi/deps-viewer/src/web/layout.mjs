/**
 * 力导向布局（Fruchterman-Reingold 变体）
 *
 * 说明：
 * - 零依赖，结果确定（初始位置按黄金角生成，不用随机数），每次渲染布局一致
 * - 节点规模约 86、边约 150，O(n²) 的斥力计算完全够用，400 次迭代在毫秒级
 * - 除斥力/弹簧外额外加了「类型分层力」：库被拉向左半区、扩展被拉向右半区，
 *   让依赖方向整体自左向右，图更易读
 * - 本文件会被内联进单文件 HTML，因此只用 export function，不要引入任何依赖
 */

const DEFAULTS = {
  width: 1500,
  height: 950,
  iterations: 400,
  linkDistance: 78,
  spring: 0.035,
  layerStrength: 0.02, // 水平分层力
  centerStrength: 0.016, // 垂直向心力
  damping: 0.82,
  maxSpeed: 14,
  padding: 44,
  alphaStart: 0.08,
  alphaEnd: 0.004,
};

/** 黄金角，用于生成分布均匀且确定的初始位置 */
const GOLDEN_ANGLE = 2.399963229728653;

/**
 * 创建力导向模拟
 * @param {Array} nodes 节点数组，会被就地写入 x/y/vx/vy
 * @param {Array} edges 边数组 [{ source, target }]，source/target 为节点 id
 * @param {Object} options 覆盖 DEFAULTS
 */
export function createSimulation(nodes, edges, options = {}) {
  const cfg = { ...DEFAULTS, ...options };
  const { width, height, iterations, linkDistance, spring } = cfg;
  const n = nodes.length;

  const byId = new Map(nodes.map((node) => [node.id, node]));
  const links = edges
    .map((edge) => [byId.get(edge.source), byId.get(edge.target)])
    .filter(([a, b]) => a && b);

  // 期望边长：节点越多，单节点可用面积越小
  const k = n > 0 ? Math.sqrt((width * height) / n) : 0;

  let iteration = 0;

  function initPositions() {
    nodes.forEach((node, i) => {
      const angle = i * GOLDEN_ANGLE;
      const isLib = node.type === 'library';
      node.x = (isLib ? width * 0.33 : width * 0.7) + 130 * Math.cos(angle);
      node.y = height * 0.5 + 130 * Math.sin(angle);
      node.vx = 0;
      node.vy = 0;
    });
  }

  /** 推进 steps 步迭代；steps 省略时跑完剩余迭代 */
  function tick(steps = 1) {
    if (n < 2) {
      iteration = iterations;
      return;
    }
    const end = Math.min(iterations, iteration + steps);

    for (; iteration < end; iteration++) {
      const t = iteration / iterations;
      const alpha = cfg.alphaStart * (1 - t) * (1 - t) + cfg.alphaEnd;

      // 斥力：任意两节点互相排斥
      for (let i = 0; i < n; i++) {
        const a = nodes[i];
        for (let j = i + 1; j < n; j++) {
          const b = nodes[j];
          let dx = a.x - b.x;
          let dy = a.y - b.y;
          let d2 = dx * dx + dy * dy;
          if (d2 < 1) {
            // 完全重合时给一个确定性的微小偏移，避免除零
            dx = i % 2 === 0 ? 0.5 : -0.5;
            dy = 0.5;
            d2 = 0.5;
          }
          const d = Math.sqrt(d2);
          const f = (k * k) / d2;
          const fx = (dx / d) * f;
          const fy = (dy / d) * f;
          a.vx += fx;
          a.vy += fy;
          b.vx -= fx;
          b.vy -= fy;
        }
      }

      // 弹簧引力：有依赖关系的节点互相靠近
      for (const [a, b] of links) {
        const dx = b.x - a.x;
        const dy = b.y - a.y;
        const d = Math.hypot(dx, dy) || 1;
        const f = (d - linkDistance) * spring;
        const fx = (dx / d) * f;
        const fy = (dy / d) * f;
        a.vx += fx;
        a.vy += fy;
        b.vx -= fx;
        b.vy -= fy;
      }

      // 分层重力 + 阻尼 + 限速 + 位移
      for (const node of nodes) {
        const targetX = node.type === 'library' ? width * 0.33 : width * 0.7;
        node.vx += (targetX - node.x) * cfg.layerStrength;
        node.vy += (height * 0.5 - node.y) * cfg.centerStrength;

        node.vx *= cfg.damping;
        node.vy *= cfg.damping;

        const speed = Math.hypot(node.vx, node.vy);
        if (speed > cfg.maxSpeed) {
          node.vx = (node.vx / speed) * cfg.maxSpeed;
          node.vy = (node.vy / speed) * cfg.maxSpeed;
        }

        if (node.fixed) {
          // 被拖拽的节点固定在指针位置
          node.x = node.fx;
          node.y = node.fy;
          node.vx = 0;
          node.vy = 0;
        } else {
          node.x += node.vx * alpha;
          node.y += node.vy * alpha;
        }
      }
    }
  }

  /** 把布局结果平移缩放，使其铺满视口并留出边距 */
  function normalize() {
    if (n === 0) return;
    let minX = Infinity;
    let minY = Infinity;
    let maxX = -Infinity;
    let maxY = -Infinity;
    for (const node of nodes) {
      if (!Number.isFinite(node.x) || !Number.isFinite(node.y)) continue;
      if (node.x < minX) minX = node.x;
      if (node.y < minY) minY = node.y;
      if (node.x > maxX) maxX = node.x;
      if (node.y > maxY) maxY = node.y;
    }
    if (!Number.isFinite(minX)) return;

    const w = Math.max(maxX - minX, 1);
    const h = Math.max(maxY - minY, 1);
    const scale = Math.min(
      (width - cfg.padding * 2) / w,
      (height - cfg.padding * 2) / h,
      1.6
    );
    const offsetX = (width - w * scale) / 2;
    const offsetY = (height - h * scale) / 2;

    for (const node of nodes) {
      node.x = (node.x - minX) * scale + offsetX;
      node.y = (node.y - minY) * scale + offsetY;
    }
  }

  function run() {
    initPositions();
    tick(iterations);
    normalize();
    return nodes;
  }

  return { run, tick, normalize, config: cfg, get iteration() { return iteration; } };
}

/** 一次性跑完布局的便捷入口 */
export function forceLayout(nodes, edges, options = {}) {
  return createSimulation(nodes, edges, options).run();
}
