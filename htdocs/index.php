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
 * This is the main application fail which sets up the routing for silex
 *
 * @author Markus Tacker <m@coderbyheart.de>
 */

/**
 * Include local configuration
 */
require_once 'config.php';

/**
 * Include required libs
 */
require_once __DIR__ . '/../vendor/silex.phar';
require_once __DIR__ . '/../lib/StonesReader.php';
require_once __DIR__ . '/../lib/Model/Stone.php';
require_once __DIR__ . '/../vendor/TwigExtensions/lib/Twig/Extensions/Autoloader.php';
Twig_Extensions_Autoloader::register();

$stonesReader = new StonesReader($config['stonesCSVFile']);

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
$app['config'] = $app->share(function () use($app, $config)
{
    return $config;
});

$app->before(function () use ($app, $config)
{
    $locale = $config['locales']['en'];
    if ($lang = $app['request']->get('lang')) {
        if (isset($config['locales'][$lang])) $locale = $config['locales'][$lang];
    }
    putenv('LC_ALL=' . $locale);
    setlocale(LC_ALL, $locale);
    bindtextdomain('nowhere', __DIR__ . '/../locale/');
    bind_textdomain_codeset('nowhere', 'UTF-8');
    textdomain('nowhere');
    $app['twig']->addGlobal('lang', $lang);
    $app['twig']->addGlobal('langswitchlink', preg_replace('%^/' . $lang . '/%', '/' . ($lang === 'en' ? 'de' : 'en') . '/', $app['request']->getPathInfo()));
    $app['twig']->addGlobal('contactmail', $config['mail_from']);
});

$app->get('/', function() use($app)
{
    return $app->redirect('/en/stones');
});

$app->get('/{lang}', function($lang) use($app)
{
    return $app->redirect("/$lang/stones");
})->assert('lang', '[a-z]{2}');

$app->get('/{lang}/stones', function($lang) use($app, $stonesReader)
{
    setcookie('lastloc', '/' . $lang . '/stones', 0, '/');
    return $app['twig']->render('stones.twig', array('navactive' => 'stones', 'stones' => $stonesReader->getStones()));
})->assert('lang', '[a-z]{2}');

$app->get('/{lang}/stones/list', function($lang) use($app, $stonesReader)
{
    setcookie('lastloc', '/' . $lang . '/stones/list', 0, '/');
    return $app['twig']->render('stones-list.twig', array('navactive' => 'stones', 'stones' => $stonesReader->getStones()));
})->assert('lang', '[a-z]{2}');

$app->get('/{lang}/places', function($lang) use($app, $stonesReader)
{
    setcookie('lastloc', '/' . $lang . '/places', 0, '/');
    return $app['twig']->render('places.twig', array('navactive' => 'places', 'stones' => $stonesReader->getStones()));
})->assert('lang', '[a-z]{2}');

$app->get('/{lang}/coordinates', function($lang) use($app, $stonesReader)
{
    return $app['twig']->render('coordinates.twig', array('navactive' => 'coordinates', 'stones' => $stonesReader->getStones()));
})->assert('lang', '[a-z]{2}');

$app->get('/{lang}/stone/{id}', function($lang, $id) use($app, $stonesReader)
{
    return $app['twig']->render('stone.twig', array('navactive' => 'stones', 'stone' => $stonesReader->getStone($id)));
})->assert('lang', '[a-z]{2}');

$app->get('/{lang}/stone/{id}/place', function($lang, $id) use($app, $stonesReader)
{
    return $app['twig']->render('place.twig', array('navactive' => 'places', 'stone' => $stonesReader->getStone($id)));
})->assert('lang', '[a-z]{2}');

$app->get('/{lang}/stone/{id}/coordinates', function($lang, $id) use($app, $stonesReader)
{
    return $app['twig']->render('coordinate.twig', array('navactive' => 'coordinates', 'stone' => $stonesReader->getStone($id)));
})->assert('lang', '[a-z]{2}');

$app->get('/{lang}/contact', function($lang) use($app)
{
    return $app['twig']->render('contact.twig', array('navactive' => 'contact'));
})->assert('lang', '[a-z]{2}');

$app->get('/{lang}/about', function($lang) use($app)
{
    return $app['twig']->render('about.twig', array('navactive' => 'about'));
})->assert('lang', '[a-z]{2}');

$app->get('/{lang}/participate', function($lang) use($app)
{
    return $app['twig']->render('participate.twig', array('navactive' => 'participate'));
})->assert('lang', '[a-z]{2}');

$app->post('/{lang}/participate', function($lang) use($app)
{
    $body = '';
    foreach (array('name', 'email', 'url', 'lat', 'lng', 'street', 'zip', 'city', 'country') as $k) {
        $body .= $k . ': ' . $app['request']->get($k) . PHP_EOL;
    }

    try {
        $message = \Swift_Message::newInstance()
            ->setSubject('[Nowhere] Neuer Stein')
            ->setFrom(array($app['config']['mail_from']))
            ->setTo($app['config']['mail_to'])
            ->setBody($body)
            ->attach(\Swift_Attachment::fromPath($app['request']->files->get('photo')->getPathname(), $app['request']->files->get('photo')->getMimeType()));

        $app['mailer']->send($message);
    } catch (\Swift_TransportException $e) {
        $app->abort(500, 'Could not send mail: ' . $e->getMessage());
    }

    return $app['twig']->render('participate-ok.twig', array('navactive' => 'participate'));
})->assert('lang', '[a-z]{2}');

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
})->assert('lang', '[a-z]{2}');

$app->run();
