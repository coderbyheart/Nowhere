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
    'h1' => array(
        'bg' => '#e7ebeb',
        'fg' => '#445C98',
        'size' => 28,
        'transparent' => true,
        'font' => '../extra/Brown-Bold.otf',
        'trim' => true,
    ),
    'listing' => array(
        'bg' => '#e7ebeb',
        'fg' => '#445C98',
        'size' => 12,
        'transparent' => true,
        'font' => '../extra/Brown-Regular.otf',
        'trim' => true,
    ),
);
$config['coordinates-large'] = $config['coordinates'];
$config['coordinates-large']['size'] = 80;
$config['coordinates-large']['trim'] = true;
unset($config['coordinates-large']['width']);
unset($config['coordinates-large']['height']);
$config['coordinates-large-over'] = $config['coordinates-large'];
$config['coordinates-large-over']['fg'] = '#ffffff';
$config['h2'] = $config['h1'];
$config['h2']['fg'] = '#000000';
$config['h2-over'] = $config['h2'];
$config['h2-over']['fg'] = '#ffffff';
$config['h2-active'] = $config['h2'];
$config['h2-active']['fg'] = '#EB6C5F';
$config['h2-active-over'] = $config['h2-over'];
$config['nav'] = $config['h1'];
$config['nav']['fg'] = '#000000';
$config['nav']['size'] = 15;
$config['nav-over'] = $config['nav'];
$config['nav-over']['fg'] = '#ffffff';
$config['nav-active'] = $config['nav'];
$config['nav-active']['fg'] = '#EB6C5F';
$config['nav-active-over'] = $config['nav-over'];
$config['listing-small'] = $config['listing'];
$config['listing-small']['size'] = 9;
$config['listing-small']['font'] = '../extra/Brown-Bold.otf';
$config['listing-over'] = $config['listing'];
$config['listing-over']['fg'] = '#EB6C5F';

$class = (isset($_GET['class'])) ? $_GET['class'] : 'menu';

if (!isset($config[$class])) {
    header("HTTP/1.0 Not found", true, 404);
    echo "Invalid class: " . $class;
    return;
}

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

$width = isset($config[$class]['width']) ? $config[$class]['width'] : 3000;
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

