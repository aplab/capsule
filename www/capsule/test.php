<?php include '../../Capsule/lib/Capsule/Capsule.php';
use Capsule\Capsule;

use Capsule\User\Auth;
use Capsule\Tools\Mysql;
use Capsule\Loader\GeSHi;
use Usr\Aplab\Model\DevLog;
use Capsule\Plugin\Storage\Storage;


header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', true);
$app = Capsule::getInstance();
if (!Auth::getInstance()->currentUser) die;

// GeSHi::getInstance();
// $geshi = new \GeSHi(file_get_contents(__FILE__), 'php');
// $geshi->enable_classes(false);
// $geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);
// echo $geshi->parse_code();

// $o = DevLog::id(106);
// \Capsule\Tools\Tools::dump($o->earlierItemId());
// \Capsule\Tools\Tools::dump($o->laterItemId());

$dir = Storage::getInstance()->readDir('ololo');

foreach ($dir as $k => $v) {
    \Capsule\Tools\Tools::dump($v->getFilename());
}

Storage::getInstance()->addFile(__FILE__, '/ololo/jaja1.php', true);

//Storage::getInstance()->dropLink('/ololo/jaja1.php');

$i = new \SplFileInfo(__FILE__);
\Capsule\Tools\Tools::dump(stat(__FILE__));

