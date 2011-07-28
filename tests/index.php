<?
require_once '../libs/dude/cookie.php';

error_reporting(-1);
ini_set("display_errors", "on");

while (ob_list_handlers())
    ob_end_clean();

$config = array();
$config['path'] = "/DudeCookie/trunk";
$config['domain'] = '.' . preg_replace('#^www\.#i', '', $_SERVER['HTTP_HOST']);
$config['expire'] = 86400; // one day
$config['secure'] = true;
$config['httponly'] = true;

$cookie = new Dude_Cookie();
$cookie->testcookie = true;
$cookie['arrayaccesstest'] = 1;

ob_start();
echo "<pre>";
print_r($cookie->getAll());

unset($cookie['testcookie']);
print_r($cookie->getAll());
echo "</pre>";

ob_end_flush();

echo "Apache headers sent:<br/>&mdash; ";
echo join("<br/>&mdash; ", headers_list()) . "<br/><br/>";

// errors handling preview :D
$cookie->set("cookie_name", "cookie_value");