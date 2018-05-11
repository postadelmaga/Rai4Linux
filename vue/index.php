<?php
define('MAGENTO_ROOT', getcwd());

$coreFilename = MAGENTO_ROOT . '/app/Vue.php';

if (!file_exists($coreFilename)) {
    if (is_dir('downloader')) {
        header("Location: downloader");
    } else {
        echo $coreFilename . " was not found";
    }
    exit;
}
require MAGENTO_ROOT . '/app/bootstrap.php';
require_once $coreFilename;

ini_set('display_errors', 1);

umask(0);
?>

<?php
if (isset($_GET['ajax'])) {
    Vue::run('ajax');
} else {
    Vue::run();
}
?>
