<?php

error_reporting(-1);
ini_set('display_errors', 1);
setlocale(LC_ALL, 'de_DE.utf8');

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
    $stones = array();
    $fp = fopen_utf8('./media/stones.csv', 'r');
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
    foreach(getStones() as $Stone) {
        if ($Stone->getNumber() == $id) return $Stone;
    }
}

require_once __DIR__ . '/../vendor/Silex/autoload.php';
require_once __DIR__ . '/../lib/Model/Stone.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../templates',
    'twig.class_path' => __DIR__ . '/../vendor/Silex/vendor/twig/lib',
));

$app->get('/', function() use($app)
{
    return $app['twig']->render('index.twig', array('stones' => getStones()));
});

$app->get('/en/places', function() use($app)
{

    return $app['twig']->render('places.twig', array('stones' => getStones()));
});

$app->get('/en/coordinates', function() use($app)
{

    return $app['twig']->render('coordinates.twig', array('stones' => getStones()));
});

$app->get('/stone/{id}', function($id) use($app)
{

    return $app['twig']->render('stone.twig', array('stone' => getStone($id)));
});

$app->get('/stone/{id}/place', function($id) use($app)
{

    return $app['twig']->render('place.twig', array('stone' => getStone($id)));
});

$app->get('/stone/{id}/coordinates', function($id) use($app)
{

    return $app['twig']->render('coordinate.twig', array('stone' => getStone($id)));
});

$app->run();