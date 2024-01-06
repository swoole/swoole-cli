

ovn-nbctl -- --id=@nat create nat type="snat" logical_ip=10.1.20.0/24 \
external_ip=10.4.20.1 -- add logical_router lr02 nat @nat
