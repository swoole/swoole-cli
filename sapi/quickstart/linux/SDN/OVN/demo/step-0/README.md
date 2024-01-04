# 交换机配置端口

```bash
ovn-nbctl ls-add logical-switch-name


ovn-nbctl lsp-add ls01 ls01_port02
ovn-nbctl lsp-set-addresses ls01_port02 '00:02:00:00:00:02 10.1.20.2'
ovn-nbctl lsp-set-port-security ls01_port02  '00:02:00:00:00:02 10.1.20.2'


```
