<?php

    error_reporting( E_ALL );
    ini_set( 'display_errors', 1 );

    // Parameter for Image creation
    $config = array(
        'h2' => array(
            'bg' => '#ffffff',
            'fg' => '#3c3c3c',
            'size' => 22,
            'transparent' => true,
            'font' => 'AUdimat-Bold.otf',
        ),
	);

    $class = (isset($_GET['class'])) ? $_GET['class'] : 'menu';
    $text = (isset($_GET['text'])) ? $_GET['text'] : 'No text supplied';

    // Check if redirected from 404
    if (isset($_SERVER['REDIRECT_STATUS'])
    and $_SERVER['REDIRECT_STATUS'] == '404'
    and preg_match('%^' . dirname($_SERVER['PHP_SELF']) .'/dynamic/([^/]+)/(.+).png$%', $_SERVER['REDIRECT_URL'], $match)) {
        $class = $match[1];
        $text = $match[2];
		$file = $match[2];
    }
	
	$text = base64_decode( $text );
	$text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );

    $target = sprintf('%s/%s.png', $class, $file);

    $oldmask = umask(0000);
    if (!is_dir(dirname($target))) {
        if (!mkdir(dirname($target), 0700, true)) {
            throw new Exception(sprintf(gettext('"%s" is not writeable.'), $target));
        }
        chmod(dirname($target), 0777);
    }

    if (!is_writeable(dirname($target))) {
        throw new Exception(sprintf(gettext('"%s" is not writeable.'), $target));
    }

    $cmd = 'convert -size 2000x400 ';
    $cmd .= sprintf('xc:"%s" ', $config[$class]['bg']);
    if ($config[$class]['transparent']) {
        $cmd .= sprintf('-transparent "%s" ', $config[$class]['bg']);
    }
    $cmd .= sprintf('-fill "%s" ', $config[$class]['fg']);
    $cmd .= '-font ' . $config[$class]['font'] . ' ';
    $cmd .= sprintf('-pointsize %d ', $config[$class]['size'] * 4);
    $cmd .= sprintf('-draw "text 0,%d \'%s\'" ', $config[$class]['size'] * 4, $text);
    $cmd .= '-resize 25% ';
    $cmd .= '-trim +repage ';
    if (isset($config[$class]['border'])) {
        $cmd .= sprintf('-bordercolor "%s" ', $config[$class]['bg']);
        $cmd .= sprintf('-border %d ', $config[$class]['border']);
    }
    $cmd .= escapeshellarg($target);

    exec($cmd, $out, $return);
    umask($oldmask);

    if ($return != 0) {
        throw new Exception(sprintf(gettext('Command failed: "%s" width return code %d.'), $cmd, $return));
    }

    header('Content-Type: image/png');
    echo file_get_contents($target);

