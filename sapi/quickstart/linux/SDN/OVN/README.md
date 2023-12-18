
```bash
ovn-nbctl show
ovn-sbctl show

ovn-sbctl lflow-list

ovn-sbctl list chassis


ovn-nbctl get-connection
ovn-sbctl get-connection

ss -tuxlpn | grep -e '^\s*tcp\s.*\b:664[0-5]\b' -e '^\s*udp\s.*\b:6081\b' -e '^\s*u_str\s.*\bovn\b' | sed -r -e 's/\s+$//'

```
