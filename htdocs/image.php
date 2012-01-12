<?php

error_reporting(-1);
ini_set('display_errors', 1);

// Parameter for Image creation
$config = array(
    'coordinates' => array(
        'bg' => '#e7ebeb',
        'fg' => '#000000',
        'size' => 40,
        'transparent' => true,
        'font' => '../extra/Brown-Bold.otf',
        'width' => 1600,
        'height' => 226,
    ),
);

$class = (isset($_GET['class'])) ? $_GET['class'] : 'menu';
$text = (isset($_GET['text'])) ? $_GET['text'] : 'No text supplied';
$target = sprintf('assets/textimg/%s.png', md5($class . '-' . $text));

if (is_file($target)) {
    header('Content-Type: image/png');
    echo file_get_contents($target);
    return;
}

$oldmask = umask(0000);

if (!is_writeable(dirname($target))) {
    throw new Exception(sprintf(gettext('"%s" is not writeable.'), $target));
}

$width = isset($config[$class]['width']) ? $config[$class]['width'] : 2000;
$height = isset($config[$class]['height']) ? $config[$class]['height'] : 400;
$cmd = sprintf('convert -size %dx%d ', $width, $height);
$cmd .= sprintf('xc:"%s" ', $config[$class]['bg']);
if ($config[$class]['transparent']) {
    $cmd .= sprintf('-transparent "%s" ', $config[$class]['bg']);
}
$cmd .= sprintf('-fill "%s" ', $config[$class]['fg']);
$cmd .= '-gravity center ';
$cmd .= '-font ' . $config[$class]['font'] . ' ';
$cmd .= sprintf('-pointsize %d ', $config[$class]['size'] * 4);
$cmd .= sprintf('-draw "text 0,%d \'%s\'" ', 0, str_replace("'", '', $text));
$cmd .= '-resize 25% ';
if (isset($config[$class]['trim']) && $config[$class]['trim']) $cmd .= '-trim ';
$cmd .= escapeshellarg($target);

exec($cmd, $out, $return);
umask($oldmask);

if ($return != 0) {
    throw new Exception(sprintf(gettext('Command failed: "%s" width return code %d.'), $cmd, $return));
}

header('Content-Type: image/png');
echo file_get_contents($target);

