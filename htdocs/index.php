<?php

error_reporting(-1);
ini_set('display_errors', 1);
setlocale(LC_ALL, 'de_DE.utf8');

$stonesCsvURL = 'https://docs.google.com/spreadsheet/pub?hl=de&hl=de&key=0AtTPpgm7INxMdDB4Qm42QWZrNEtKRTdkUHAwWjJSTkE&single=true&gid=0&output=csv';
$cacheFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'nowhere.csv';
if (!is_file($cacheFile) || filemtime($cacheFile) < time() - 3600) copy($stonesCsvURL, $cacheFile);

function fopen_utf8($filename, $mode)
{
    $file = @fopen($filename, $mode);
    $bom = fread($file, 3);
    if ($bom != "\xEF\xBB\xBF")
        rewind($file, 0);
    return $file;
}

/**
 * @return Stone[]
 */
function getStones()
{
    global $cacheFile;
    $stones = array();
    $fp = fopen_utf8($cacheFile, 'r');
    while ($stoneData = fgetcsv($fp)) {
        $stone = new Stone();
        $stone->setNumber($stoneData[0]);
        $stone->setCountry($stoneData[1]);
        $stone->setLocality($stoneData[2]);
        $stone->setPerson($stoneData[3]);
        $stone->setLat($stoneData[4]);
        $stone->setLng($stoneData[5]);
        $stones[] = $stone;
    }
    fclose($fp);
    return $stones;
}

/**
 * @param int $id
 * @return Stone
 */
function getStone($id)
{
    foreach (getStones() as $Stone) {
        if ($Stone->getNumber() == $id) return $Stone;
    }
}

require_once __DIR__ . '/../vendor/silex.phar';
require_once __DIR__ . '/../lib/Model/Stone.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../templates',
    'twig.class_path' => __DIR__ . '/../vendor/twig/lib',
));

$app->get('/', function() use($app)
{
    return $app->redirect('/en/stones');
});

$app->get('/{lang}', function($lang) use($app)
{
    return $app->redirect("/$lang/stones");
});

$app->get('/{lang}/stones', function($lang) use($app)
{
    return $app['twig']->render('stones.twig', array('lang' => $lang, 'navactive' => 'stones', 'stones' => getStones()));
});

$app->get('/{lang}/stones/list', function($lang) use($app)
{
    return $app['twig']->render('stones-list.twig', array('lang' => $lang, 'navactive' => 'stones', 'stones' => getStones()));
});

$app->get('/{lang}/places', function($lang) use($app)
{
    return $app['twig']->render('places.twig', array('lang' => $lang, 'navactive' => 'places', 'stones' => getStones()));
});

$app->get('/{lang}/coordinates', function($lang) use($app)
{
    return $app['twig']->render('coordinates.twig', array('lang' => $lang, 'navactive' => 'coordinates', 'stones' => getStones()));
});

$app->get('/{lang}/stone/{id}', function($lang, $id) use($app)
{
    return $app['twig']->render('stone.twig', array('lang' => $lang, 'navactive' => 'stones', 'stone' => getStone($id)));
});

$app->get('/{lang}/stone/{id}/place', function($lang, $id) use($app)
{
    return $app['twig']->render('place.twig', array('lang' => $lang, 'navactive' => 'places', 'stone' => getStone($id), 'size' => getimagesize('media' . DIRECTORY_SEPARATOR . 'stones' . DIRECTORY_SEPARATOR . $id . '-place-2048.jpg')));
});

$app->get('/{lang}/stone/{id}/coordinates', function($lang, $id) use($app)
{
    return $app['twig']->render('coordinate.twig', array('lang' => $lang, 'navactive' => 'coordinates', 'stone' => getStone($id), 'size' => getimagesize('media' . DIRECTORY_SEPARATOR . 'stones' . DIRECTORY_SEPARATOR . $id . '-place-2048.jpg')));
});

$app->get('/{lang}/contact', function($lang) use($app)
{
    return $app['twig']->render('contact.twig', array('lang' => $lang, 'navactive' => 'contact'));
});

$app->get('/{lang}/about', function($lang) use($app)
{
    return $app['twig']->render('about.twig', array('lang' => $lang, 'navactive' => 'about'));
});

$app->get('/{lang}/participate', function($lang) use($app)
{
    return $app['twig']->render('participate.twig', array('lang' => $lang, 'navactive' => 'participate'));
});

$app->get('/{lang}/news', function($lang) use($app)
{
    return $app['twig']->render('news.twig', array('lang' => $lang, 'navactive' => 'news'));
});

$app->run();
