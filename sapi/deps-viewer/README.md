# swoole-cli 构建依赖可视化工具

把当前 swoole-cli 构建配置中的库与扩展依赖关系导出为
**单文件交互式 HTML 依赖图**，可在 `file://` 下直接打开。

特性：
- 自动按文件名/PECL/内置/swoole 解析版本号
- 区分内置扩展（推断为 PHP License）与已声明的 license
- 力导向布局（86 节点 30ms 左右，无 NaN，确定性）
- 节点拖拽、滚轮缩放、点击高亮上下游
- 按类型/许可证/边类型筛选
- 搜索定位 + 依赖/被依赖点击跳转
- 暗色/亮色主题

零 npm 依赖（只使用 Node 内置模块）。

完整教程见 [docs/dependency-graph.md](../../docs/dependency-graph.md)，
本文档侧重工具的目录结构与 JSON 数据格式。

## 用法

```bash
# 安装：无需 npm install

# 1. 生成依赖数据（PHP 侧，调用 ./make.sh 的同一套配置）
cd /path/to/swoole-cli
php prepare.php --with-dependency-json=1
# 产物：bin/dependency-graph.json

# 2. 校验数据
cd sapi/deps-viewer
node bin/deps-viewer.mjs check

# 3. 生成网页
node bin/deps-viewer.mjs build
# 产物：dist/index.html（双击即开）

# 4. 验证布局算法（耗时、间距、确定性）
node bin/deps-viewer.mjs verify

# 一条命令跑全部
npm run all    # 实际是 node bin/deps-viewer.mjs 的脚本别名
```

`php prepare.php` 的具体行为见 `docs/options.md` 的
`with-dependency-json` 段。

## 输出数据格式

`bin/dependency-graph.json` 结构：

```json
{
  "meta": { "phpVersion": "8.4.14", "swooleVersion": "v6.2.0", "counts": {...} },
  "licenseTypeNames": { "0": "Custom", "5": "MIT", "6": "PHP License" },
  "nodes": [
    {
      "id": "lib:curl",          // 必须带 lib: / ext: 前缀，因为有同名（curl 既是库也是扩展）
      "type": "library",         // library | extension
      "name": "curl",
      "version": "8.16.0",
      "versionSource": "source-file",  // source-file | pecl | swoole | php
      "licenseType": 0,              // 枚举值
      "licenseName": "Custom",       // 人类可读
      "licenseInferred": false,      // true 表示按 php-src 内置扩展推断
      "licenseUrl": "https://...",
      "homePage": "https://curl.se/",
      "manual": "https://...",
      "sourceFile": "curl-8.16.0.tar.gz"
    }
  ],
  "edges": [ { "source": "ext:curl", "target": "lib:curl", "kind": "ext-lib" } ]
}
```

## 目录结构

```
sapi/deps-viewer/
  package.json            零依赖，仅定义 scripts
  bin/deps-viewer.mjs     CLI 入口（check | build | verify）
  src/
    load.mjs              读 JSON、校验、补全
    stats.mjs             统计与格式化输出
    render.mjs            内联 CSS/JS/数据 → 单一 HTML
    web/
      layout.mjs          力导向布局
      app.mjs              前端 UI 与交互
  assets/
    template.html         HTML 骨架
    app.css               样式
  dist/                   产物（gitignore）
```

## 实现说明

- **数据流**：`php prepare.php --with-dependency-json=1` → `bin/dependency-graph.json` → `node build` → `dist/index.html`
- **版本解析** 写在 PHP 模板里（`sapi/src/template/dependency_json.php`），覆盖
  `libxml2-v2.9.14`、`icu4c-73_2-src`、`sqlite-autoconf-3430200`、
  `libx265_master`、`gmp-6.3.0.tar.lz` 等特殊命名
- **力导向布局** 自实现（Fruchterman-Reingold 变体），O(n²) 完全够用，
  额外加「库偏左、扩展偏右」的水平分层力让图更易读
- **零外部资源**：CSS/JS/数据全部内联，可直接 `file://` 打开，不会触发任何网络请求
- **zuo 的 library 与 extension 同名处理**：id 统一加 `lib:` / `ext:` 前缀

## 已知限制

- 节点标签始终显示，缩放过小时可能重叠（按需可加缩放阈值）
- 内置扩展的 license 推断为 PHP License（php-src 采用 PHP License 3.01），
  数据中通过 `licenseInferred: true` 标记
- 仅展示当前构建配置（48 库 + 38 扩展）；如需展示全部 57 库/48 扩展，需要
  改 `php prepare.php` 不传 `--enabled-extensions` 过滤（当前 Preprocessor 没有暴露此开关）
