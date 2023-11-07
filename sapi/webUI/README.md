# webUI

## 启动 web ui

```shell
# 准备数据
bash sapi/webUI/sync-webui-data.sh

# 启动
php sapi/webUI/bootstrap.php

```

## 准备
1. 工作流 workflow
1. 状态机 state machine (由状态、事件和转换组成)
1. 领域驱动设计 Domain Driven Design (最大的价值是梳理业务性需求，将不同的业务领域划分出来，并形成领域之间的接口交互)


## 工具
1. [xterm](http://xtermjs.org/)
1. [WebSSH2](https://github.com/billchurch/webssh2.git)
1. [公共 CDN 静态资源库](https://github.com/justjavac/ReplaceGoogleCDN/blob/master/public-cdn.md)
1. [workflow](https://symfony.com/doc/current/workflow.html)
