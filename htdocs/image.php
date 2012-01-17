<?php

// +----------------------------------------------------+
// | The Project Nowhere website is free software:      |
// | you can redistribute it and/or modify it under     |
// | the terms of the GNU General Public License as     |
// | published by the Free Software Foundation,         |
// | either version 3 of the License, or (at your       |
// | option) any later version.                         |
// |                                                    |
// | In addition you are required to retain all         |
// | author attributions provided in this software      |
// | and attribute all modifications made by you        |
// | clearly and in an appropriate way.                 |
// |                                                    |
// | This software is distributed in the hope that      |
// | it will be useful, but WITHOUT ANY WARRANTY;       |
// | without even the implied warranty of               |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR        |
// | PURPOSE.  See the GNU General Public License for   |
// | more details.                                      |
// |                                                    |
// | You should have received a copy of the GNU         |
// | General Public License along with this software.   |
// | If not, see <http://www.gnu.org/licenses/>.        |
// +----------------------------------------------------+

/**
 * This simple script renders text images
 *
 * @author Markus Tacker <m@coderbyheart.de>
 */

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

$config['label'] = $config['h1'];
$config['label-small'] = $config['label'];
$config['label-small']['size'] = 18;
$config['button'] = $config['h1'];
$config['button']['fg'] = '#ffffff';
$config['button']['size'] = 22;

$class = (isset($_GET['class'])) ? $_GET['class'] : 'menu';

if (!isset($config[$class])) {
    header("HTTP/1.0 404 Not found", true, 404);
    echo "Invalid class: " . $class;
    return;
}

$text = (isset($_GET['text'])) ? $_GET['text'] : 'No text supplied';
$target = sprintf('assets/textimg/%s.png', md5($class . '-' . $text));

if (is_file($target)) {
    header('Content-Type: image/png');
    readfile($target);
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
readfile($target);

