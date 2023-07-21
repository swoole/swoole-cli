echo " install deps "

__CURRENT_DIR__=$(cd "$(dirname "$0")";pwd)

<?= implode(PHP_EOL, $this->preInstallCommands) .PHP_EOL ?>
