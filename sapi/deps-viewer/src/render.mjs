import { readFileSync, writeFileSync, mkdirSync } from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const HERE = dirname(fileURLToPath(import.meta.url));
export const PKG_ROOT = resolve(HERE, '..');

/**
 * 把源码片段里的 ES module 语法剥掉，以便顺序拼接进单个 IIFE
 *
 * 只处理本项目自己写的两种形式：
 *   import { a, b } from './x.mjs';
 *   export function foo(...)  /  export const bar = ...
 */
function stripModuleSyntax(src) {
  return src
    .replace(/^import\s+[^;]*;\s*$/gm, '')
    .replace(/^export\s+(?=(?:async\s+)?(?:function|const|let|class)\b)/gm, '');
}

/**
 * 生成单文件 HTML：内嵌 CSS、JS 与数据，可直接在 file:// 下打开
 *
 * @param {object} data 依赖数据
 * @param {string} outFile 输出路径
 * @returns {{ outFile: string, bytes: number }}
 */
export function renderHtml(data, outFile) {
  const template = readFileSync(join(PKG_ROOT, 'assets/template.html'), 'utf8');
  const css = readFileSync(join(PKG_ROOT, 'assets/app.css'), 'utf8');
  const layoutSrc = readFileSync(join(PKG_ROOT, 'src/web/layout.mjs'), 'utf8');
  const appSrc = readFileSync(join(PKG_ROOT, 'src/web/app.mjs'), 'utf8');

  // 顺序很重要：layout 必须在 app 之前，且都包在同一个 IIFE 里
  const appJs = [
    '(function () {',
    '"use strict";',
    stripModuleSyntax(layoutSrc),
    stripModuleSyntax(appSrc),
    '})();',
  ].join('\n\n');

  // 转义 </script> 会截断标签的内容
  const dataJson = JSON.stringify(data).replace(/</g, '\\u003c');

  const html = template
    .replace('/*__CSS__*/', () => css)
    .replace('/*__DATA__*/', () => dataJson)
    .replace('/*__APP_JS__*/', () => appJs);

  if (html.includes('/*__CSS__*/') || html.includes('/*__DATA__*/') || html.includes('/*__APP_JS__*/')) {
    throw new Error('模板占位符未全部替换');
  }

  mkdirSync(dirname(outFile), { recursive: true });
  writeFileSync(outFile, html, 'utf8');
  return { outFile, bytes: Buffer.byteLength(html, 'utf8') };
}
