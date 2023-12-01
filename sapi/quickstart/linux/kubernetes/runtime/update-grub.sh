
if test $(cat /etc/default/grub | grep 'cgroup_enable=cpu' | wc -l) -eq 0 ; then
  echo 'GRUB_CMDLINE_LINUX="cgroup_enable=cpu"' >> /etc/default/grub
  update-grub2
fi






