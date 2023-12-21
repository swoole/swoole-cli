# 路由配置


```bash

ovn-nbctl lr-route-list lr01

ovn-nbctl list bfd

# ovn-nbctl create bfd logical_port=r1-sw1 dst_ip=192.168.1.1 min_tx=1000 min_rx=1000 detect_mult=3

```

```bash

# 在lr3上添加nat表项
ovn-nbctl -- --id=@nat create nat type="snat" logical_ip=10.10.10.0/24 \
external_ip=10.10.40.1 -- add logical_router lr1 nat @nat

```
