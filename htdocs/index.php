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
        $apcKey = 'nowhere-image-size-' . $stone->getNumber();
        if (!apc_exists($apcKey)) {
            apc_store($apcKey, getimagesize('media' . DIRECTORY_SEPARATOR . 'stones' . DIRECTORY_SEPARATOR . $stone->getNumber() . '-place-2048.jpg'));
        }
        $sizeinfo = apc_fetch($apcKey);
        $width = $sizeinfo[0];
        $height = $sizeinfo[1];
        $stone->setWidth($width);
        $stone->setHeight($height);
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
require_once __DIR__ . '/../vendor/TwigExtensions/lib/Twig/Extensions/Autoloader.php';
Twig_Extensions_Autoloader::register();

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\SymfonyBridgesServiceProvider(), array(
    'symfony_bridges.class_path' => __DIR__ . '/../vendor/symfony/src',
));
$app->register(new Silex\Provider\SwiftmailerServiceProvider(), array(
    'swiftmailer.class_path' => __DIR__ . '/../vendor/swiftmailer/lib/classes',
));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../templates',
    'twig.class_path' => __DIR__ . '/../vendor/twig/lib',
    'twig.configure' => $app->protect(function(\Twig_Environment $twig)
    {
        $twig->addExtension(new Twig_Extensions_Extension_I18n());
    })
));

$app->before(function () use ($app)
{
    $locale = 'en_US.utf8';
    if ($lang = $app['request']->get('lang')) {
        if ($lang == 'de') {
            $locale = 'de_DE.utf8';
        }
    }
    putenv('LC_ALL=' . $locale);
    setlocale(LC_ALL, $locale);
    bindtextdomain('nowhere', __DIR__ . '/../locale/');
    bind_textdomain_codeset('nowhere', 'UTF-8');
    textdomain('nowhere');
    $app['twig']->addGlobal('lang', $lang);
});

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
    setcookie('stones_or_places', 'stones');
    return $app['twig']->render('stones.twig', array('navactive' => 'stones', 'stones' => getStones()));
});

$app->get('/{lang}/stones/list', function($lang) use($app)
{
    setcookie('stones_or_places', 'stones');
    return $app['twig']->render('stones-list.twig', array('navactive' => 'stones', 'stones' => getStones()));
});

$app->get('/{lang}/places', function($lang) use($app)
{
    setcookie('stones_or_places', 'places');
    return $app['twig']->render('places.twig', array('navactive' => 'places', 'stones' => getStones()));
});

$app->get('/{lang}/coordinates', function($lang) use($app)
{
    return $app['twig']->render('coordinates.twig', array('navactive' => 'coordinates', 'stones' => getStones()));
});

$app->get('/{lang}/stone/{id}', function($lang, $id) use($app)
{
    return $app['twig']->render('stone.twig', array('navactive' => 'stones', 'stone' => getStone($id)));
});

$app->get('/{lang}/stone/{id}/place', function($lang, $id) use($app)
{
    return $app['twig']->render('place.twig', array('navactive' => 'places', 'stone' => getStone($id)));
});

$app->get('/{lang}/stone/{id}/coordinates', function($lang, $id) use($app)
{
    return $app['twig']->render('coordinate.twig', array('navactive' => 'coordinates', 'stone' => getStone($id)));
});

$app->get('/{lang}/contact', function($lang) use($app)
{
    return $app['twig']->render('contact.twig', array('navactive' => 'contact'));
});

$app->get('/{lang}/about', function($lang) use($app)
{
    return $app['twig']->render('about.twig', array('navactive' => 'about'));
});

$app->get('/{lang}/participate', function($lang) use($app)
{
    return $app['twig']->render('participate.twig', array('navactive' => 'participate'));
});

$app->post('/{lang}/participate', function($lang) use($app)
{

    $body = '';
    foreach(array('name', 'email', 'url', 'lat', 'lng', 'street', 'zip', 'city', 'country') as $k)  {
        $body .= $k . ': ' . $app['request']->get($k) . PHP_EOL;
    }

    try {
        $message = \Swift_Message::newInstance()
            ->setSubject('[Nowhere] Neuer Stein')
	    ->setFrom(array('hello@project-nowhere.com'))
            ->setTo(array('hello@project-nowhere.com', 'm@tacker.org'))
            ->setBody($body)
            ->attach(\Swift_Attachment::fromPath($app['request']->files->get('photo')->getPathname(), $app['request']->files->get('photo')->getMimeType()));

        $app['mailer']->send($message);
    } catch(\Swift_TransportException $e) {
        $app->abort(500, 'Could not send mail: ' . $e->getMessage());
    }

    return $app['twig']->render('participate-ok.twig', array('navactive' => 'participate'));
});

$app->get('/{lang}/news', function($lang) use($app)
{
    $media = array();
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'news')) as $file) {
        if ($file->isDir()) continue;
        $imgPath = str_replace(__DIR__, '', $file->getPathName());
        $parts = explode(DIRECTORY_SEPARATOR, $imgPath);
        if (!isset($media[$parts[3]])) $media[$parts[3]] = array();
        $media[$parts[3]][] = $imgPath;
    }
    return $app['twig']->render('news.twig', array('navactive' => 'news', 'media' => $media));
});

$app->run();
