
HOMEBREW_PREFIX=$(brew --prefix)

brew install python3

export PATH=${HOMEBREW_PREFIX}/opt/python@3/bin:${HOMEBREW_PREFIX}/opt/python@3/libexec/bin:$PATH

export PYTHONPATH=$(python -c "import site, os; print(os.path.join(site.USER_BASE, 'lib', 'python', 'site-packages'))"):$PYTHONPATH
X_PYTHON_BIN=$(python -c "import site, os; print(os.path.join(site.USER_BASE, 'bin'))")
export PATH=${X_PYTHON_BIN}:$PATH


case "$MIRROR" in
china | tuna | ustc)
  pip3 config set global.index-url https://pypi.tuna.tsinghua.edu.cn/simple
  test "$MIRROR" = "ustc" && pip3 config set global.index-url https://mirrors.ustc.edu.cn/pypi/web/simple
  ;;
tencentyun | huaweicloud)
  test "$MIRROR" = "tencentyun" && pip3 config set global.index-url https://mirrors.tencentyun.com/pypi/simple/
  test "$MIRROR" = "huaweicloud" && pip3 config set global.index-url https://repo.huaweicloud.com/pypi/simple/
  ;;
esac


which python
which pip
python --version
pip --version

python -c "import site; print(site.USER_BASE)"
pip list
which meson
which ninja

# python3 -m pip install --upgrade pip
# python3 -m pip install meson -i https://mirrors.ustc.edu.cn/pypi/web/simple
# curl https://bootstrap.pypa.io/get-pip.py -o get-pip.py


exit 0

# for  macos-12

python -m ensurepip --default-pip --upgrade --user

python -m pip --version
python -m pip install meson --user
python -m pip install ninja --user
python -m pip list


# pip install meson
# pip3 install --user meson



if [ ${WITH_UPDATE} -eq 1 ] ; then
  case "$MIRROR" in
    china|ustc)
      brew tap --custom-remote --force-auto-update homebrew/cask https://mirrors.ustc.edu.cn/homebrew-cask.git
      brew tap --custom-remote --force-auto-update homebrew/cask-versions https://mirrors.ustc.edu.cn/homebrew-cask-versions.git
      brew tap --custom-remote --force-auto-update homebrew/services https://mirrors.ustc.edu.cn/homebrew-services.git

      # 参考文档： https://help.mirrors.cernet.edu.cn/homebrew/
      # reset
      # brew tap --custom-remote --force-auto-update homebrew/cask https://github.com/Homebrew/homebrew-cask
      # brew tap --custom-remote --force-auto-update homebrew/cask-versions https://github.com/Homebrew/homebrew-cask-versions
      # brew tap --custom-remote --force-auto-update homebrew/services https://mirrors.ustc.edu.cn/homebrew-services.git
      ;;
  esac

  brew doctor
  brew update
  brew upgrade

  exit 0

fi
