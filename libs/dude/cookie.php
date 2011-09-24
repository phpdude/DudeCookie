<?php
/**
 * Cookie helper class.
 * @package DudeCms Bussiness
 * @category module
 * @copyright 2010 phpdude (http://helldude.ru)
 * @author phpdude
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @version DudeCookie v0.1 beta
 */
class Dude_Cookie implements ArrayAccess
{
    const ESCAPE_HTML_SPECIAL_CHARS = 0;
    const ESCAPE_URL_ENCODE = 1;
    const ESCAPE_HTML_ENTITIES = 2;
    const ESCAPE_RAW_URL_ENCODE = 101;
    const ESCAPE_MYSQL_ESCAPE_STRING = 102;
    const ESCAPE_MYSQL_REAL_ESCAPE_STRING = 103;

    protected $items;

    protected $expire = 0;
    protected $path = "/";
    protected $domain;
    protected $secure = false;
    protected $httpOnly = false;

    /**
     * Creates cookie helper object
     */
    public function  __construct()
    {
        $this->items = &$_COOKIE;
    }

    /**
     * Sets cookie value with default cookie options
     * @param string $name String cookie name
     * @param string $value String cookie value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    protected function prepareDomainName($domain)
    {
        $domain = trim($domain);

        if (count(array_filter(explode(".", $domain))) == 1) {
            $domain = trim($domain, ".");
        }

        return $domain;
    }

    /**
     * Sets and send a cookie
     * @param name string <p>
     * The name of the cookie.
     * </p>
     * @param value string[optional] <p>
     * The value of the cookie. This value is stored on the clients
     * computer; do not store sensitive information.
     * </p>
     * @param int|string $expire [optional] <p>

     * The time the cookie expires. This is a Unix timestamp so is
     * in number of seconds since the epoch. In other words, you'll
     * most likely set this with the time function
     * plus the number of seconds before you want it to expire. Or
     * you might use mktime.
     * time()+60*60*24*30 will set the cookie to
     * expire in 30 days. </p>
     * <p>Note: If set to 0 the cookie will expire at
     * the end of the session (when the browser closes).
     * </p>
     * <p>Note: If omit expire param then cookie will expire
     * in a time configured at config
     * </p>
     * <p>
     * WARNING: expire is compared to the client's time which can
     * differ from server's time.
     * </p>
     * @param path string[optional] <p>
     * The path on the server in which the cookie will be available on.
     * If set to '/', the cookie will be available
     * within the entire domain. If set to
     * '/foo/', the cookie will only be available
     * within the /foo/ directory and all
     * sub-directories such as /foo/bar/ of
     * domain. The default value takes from config options, if options not set is the current directory
     * that the cookie is being set in.
     * </p>
     * @param domain string[optional] <p>
     * The domain that the cookie is available.
     * To make the cookie available on all subdomains of example.com
     * then you'd set it to '.example.com'. The
     * . is not required but makes it compatible
     * with more browsers. Setting it to www.example.com
     * will make the cookie only available in the www
     * subdomain. Refer to tail matching in the
     * spec for details.
     * </p>
     * @param bool|string $secure [optional] <p>

     * Indicates that the cookie should only be transmitted over a
     * secure HTTPS connection from the client. When set to true, the
     * cookie will only be set if a secure connection exists. The default
     * is false. On the server-side, it's on the programmer to send this
     * kind of cookie only on secure connection (e.g. with respect to
     * $_SERVER["HTTPS"]).
     * </p>
     * @param bool|string $httponly [optional] <p>

     * When true the cookie will be made accessible only through the HTTP
     * protocol. This means that the cookie won't be accessible by
     * scripting languages, such as JavaScript. This setting can effectively
     * help to reduce identity theft through XSS attacks (although it is
     * not supported by all browsers). Added in PHP 5.2.0.
     * true or false
     * </p>
     * @return bool If output exists prior to calling this function,
     * setcookie will fail and return false. If
     * setcookie successfully runs, it will return true.
     * This does not indicate whether the user accepted the cookie.
     */
    public function set($name, $value, $expire = '', $path = '', $domain = '', $secure = '', $httponly = '')
    {
        if (headers_sent()) {
            throw new Exception("HTTP Headers already sent, cannot setup cookie $name");
        }

        $this->items[$name] = $value;

        $expire = $expire ? $expire : (!strcmp($expire, '0') ? 0 : $this->expire);
        $expire = $expire !== 0 && $expire < (365 * 86400) ? $expire + time() : $expire;

        $path = $path ? $path : $this->path;
        $domain = $domain ? $this->prepareDomainName($domain) : $this->domain;
        $secure = $domain ? $secure : $this->secure;
        $httponly = $httponly ? $httponly : $this->httpOnly;

        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }

    /**
     * Gives user cookie for given name
     * @param string $name Cookie name
     * @return string Returns cookie value
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Gives user cookie for given name
     * @param string $name Cookie name
     * @param string $empty Default value, if cookie doesn't exists
     * @return string Returns cookie value
     */
    public function get($name, $empty = '')
    {
        return isset($this->items[$name]) && !empty($this->items[$name]) ? $this->items[$name] : $empty;
    }

    /**
     * Get all cookies
     * @return array Returns all cookies
     */
    public function getAll()
    {
        return $this->items;
    }

    /**
     * Returns Cookie value for given name escaped via supported methods
     * @param $name
     * @param int $method Escaping method. Supported methods are
     * <b>Cookie::ESCAPE_HTMLSPECIALCHARS</b> and <b>Cookie::ESCAPE_URLENCODE</b>
     * @return string
     */
    public function getEscaped($name, $method = 1)
    {
        $value = $this->get($name);

        switch ($method)
        {
            case self::ESCAPE_MYSQL_REAL_ESCAPE_STRING:
                $value = mysql_real_escape_string($value);
                break;

            case self::ESCAPE_MYSQL_ESCAPE_STRING:
                $value = mysql_real_escape_string($value);
                break;

            case self::ESCAPE_URL_ENCODE:
                $value = urlencode($value);
                break;

            case self::ESCAPE_RAW_URL_ENCODE:
                $value = rawurldecode($value);
                break;

            case self::ESCAPE_HTML_ENTITIES:
                $value = htmlentities($value, ENT_QUOTES, "UTF-8");
                break;

            case self::ESCAPE_HTML_SPECIAL_CHARS:
            default:
                $value = htmlspecialchars($value);
                break;
        }

        return $value;
    }

    /**
     * Deletes a cookie by name
     * @param string $name Cookie name
     * @param string $path
     * @param string $domain
     * @param string $secure
     * @param string $httponly
     *
     */
    public function delete($name, $path = '', $domain = '', $secure = '', $httponly = '')
    {
        $this->set($name, '', time() - 48 * 3600, $path, $domain, $secure, $httponly);
        unset($this->items[$name]);
    }

    /**
     * Set default cookies domain
     * @param $domain string
     * @return void
     */
    public function setDomain($domain)
    {
        $this->domain = $this->prepareDomainName($domain);
    }

    /**
     * Returns default cookie domain name
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Return default expire time for cookies
     * @param $expire int Integer value, if you provide more 365*86400 will be used as absolute value or time() + value
     * for smaller values
     * @return void
     */
    public function setExpire($expire)
    {
        $this->expire = $expire < (365 * 86400) ? $expire + time() : $expire;
    }

    /**
     * Returns default expire time for cookies
     * @return int
     */
    public function getExpire()
    {
        return $this->expire;
    }

    /**
     * Set default cookie httpOnly flag for cookies
     * @param $httponly bool
     * @return void
     */
    public function setHttpOnly($httponly)
    {
        $this->httpOnly = (bool) $httponly;
    }

    /**
     * Return default cookie httpOnly flag for cookies
     * @return bool
     */
    public function getHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * Sets default cookie path
     * @param $path string
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Returns default cookie path
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set default secure flag for cookies
     * @param $secure bool
     * @return void
     */
    public function setSecure($secure)
    {
        $this->secure = $secure;
    }

    /**
     * Return secure default value for secure cookie setting flag
     * @return bool
     */
    public function getSecure()
    {
        return $this->secure;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean Returns true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }
}
