## github remote debug step

```text
1. workflow 里 zerotier/github-action 用 network_id + auth_token 加入私有网络
   → runner 获得 192.168.193.x 的虚拟 IP

2. enable-openssh.ps1 在 runner 上：
   - Add-WindowsCapability 安装 OpenSSH Server
   - Start-Service sshd，设为 Automatic
   - 防火墙放行 65527
   - 专用 ed25519 公钥写入 ~/.ssh/authorized_keys
   - 重写 sshd_config：Port 65527、PubkeyAuthentication yes、
     PasswordAuthentication no、注释掉 Match Group administrators
   - 注册表 DefaultShell 设为 PowerShell
   - Restart-Service sshd

3. 本地（也在同一 ZeroTier 网络）：
   ssh -p 65527 runneradmin@192.168.193.x
   → 直接进 PowerShell，提示符 PS D:\a\swoole-cli\swoole-cli>



```
