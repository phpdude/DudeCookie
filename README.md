# DudeCookie Project.

_Note: php5 only_

<p>Cookie manager helper class. Provides you simple but powerfull cookie management for your project. It synchronize internal $_COOKIE array with functions for cookies management, it allows you in your code make situations like:</p>

<pre><code>$cookie = new Dude_Cookie();
...
if($user)
{
    $cookie->userid = $user['id'];
    $cookie->userautologinhash = $user['uidhash'];
}
....

if($cookie->userid && $maAwesomeUsersmanager->processAutologin($_COOKIE['userautologinhash'])) { ..}</code></pre>

<p>And don't think about saving setted cokies into $_COOKIE array or delete them wneh you delete something.</p>

Basic features

-  OOP PHP5 Code and features
-  ArrayAccess class implementation: unset($cookie[$cookname])
-  Default cookies configuration via setters/getters
-  Synchronization $_COOKIE variable with setcookie() calls.