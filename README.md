# DudeCookie Project.

_Note: php5 only_

<p>Cookie manager helper class. Provides you simple but powerfull cookie management for your project. It synchronize internal $_COOKIE array with functions for cookies management, it allows you in your code make situations like:</p>

<pre>$cookie = new Dude_Cookie();
...
if($user)
{
    $cookie->userid = $user['id'];
    $cookie->userautologinhash = $user['uidhash'];
}
....

if($cookie->userid && $maAwesomeUsersmanager->processAutologin($_COOKIE['userautologinhash'])) { ..}
</pre>

<p>And don't think about saving setted cokies into $_COOKIE array or delete them wneh you delete something.</p>

* Basic features *
<ul>
    <li>OOP PHP5 Code and features</li>
    <li>ArrayAccess class implementation: unset($cookie[$cookname])</li>
    <li>Default cookies configuration via setters/getters</li>
    <li>Synchronization $_COOKIE variable with setcookie() calls.</li>
</ul>