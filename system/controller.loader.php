<?php 

/* Main Skyfire System Library */

final class fnCamelCase
{
    public static function _camelize($string)
    {
        return (string) trim($string = preg_replace_callback('/(^|_)([a-z])/', function ($m)
        {
            return strtoupper($m[2]);
        },
        $string), '_');
    }

    public static function _decamelize($string)
    {
        return (string) trim(strtolower(preg_replace('/[A-Z]/', '_$0', $string)), '_');
    }
}

class ResponseStatusCode
{
    private $view_name;
    private $data = array(); // pass along through the constructors each time

    public function __construct($view_name = FALSE, array $data = array())
    {
        $this->view_name = $view_name;
        $this->data = $data;
    }

    // TODO: return with status code
    public function statusCode($code)
    {
        if (is_readable(PARENT_DIRECTORY.'/library/packages/twig/twig/lib/Twig/Autoloader.php'))
        {
            require_once PARENT_DIRECTORY.'/library/packages/twig/twig/lib/Twig/Autoloader.php';
        }

        Twig_Autoloader::register();

        $loader = new Twig_Loader_Filesystem(PARENT_DIRECTORY.'/views');
        // TODO: if 'PARENT_DIRECTORY.'/views/_cache' doesn't exist, create the folder or throw a permission error
        $twig   = new Twig_Environment($loader, array('cache' => PARENT_DIRECTORY.'/views/_cache', 'auto_reload' => TRUE));

        echo $twig->render($this->view_name.'.twig.php', $this->data);
    }
}

class lock
{
    private $key = FALSE;
    private $code = 200;

    private $view_name;
    private $data;

    public function __construct($view_name = FALSE, array $data = array())
    {
        $this->view_name = $view_name;
        $this->data = $data;
    }

    public function statusCode($code = 200)
    {
        // if a view exist - load template
        if ($this->view_name)
        {
            $this->key = TRUE;

            $this->code = $code;
            $view = new ResponseStatusCode($this->view_name, $this->data);

            return $view->statusCode($code);
        }
    }

    public function __destruct()
    {
        if ($this->key === FALSE)
        {
            $view = new ResponseStatusCode($this->view_name, $this->data);

            return $view->statusCode($this->code);
        }
    }
}

class DisplayWith
{
    private $view_name;
    private $data;

    private $key = FALSE;
    private $code = 200;

    public function __construct($view_name = FALSE)
    {
        // checking if Twig is installed by checking the Twig folders in the library packages
        if (!dir(PARENT_DIRECTORY.'/library/packages/twig/twig/lib/Twig'))
        {
            trigger_error(
                'Twig is not installed. Please run composer or install Twig in /library/packages/',
                E_USER_ERROR
            );
        }

        $this->view_name = $view_name;
    }

    public function __destruct()
    {
        if ($this->key === FALSE)
        {
            $view = new ResponseStatusCode($this->view_name);

            return $view->statusCode($this->code);
        }
    }

    public function statusCode()
    {

    }

    public function with($data)
    {
        if (is_array($data) || is_object($data))
        {
            $this->key = TRUE;
            $this->data = (array) $data;

            return new lock($this->view_name, $this->data);
        }
        else
        {
            return !trigger_error('Argument 1 passed to with() must be either an array or object type', E_USER_ERROR);
        }
    }
}

abstract class Display
{
    // resposne status codes
    public static $status = array
    (
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
    );

    public static function getStatusCode($code)
    {
        return (string) self::$status[(int) $code];
    }

    protected function api($service_name) {}
    protected function json()
    {
        return new DisplayWith;
    }
    protected function xml()
    {
        return new DisplayWith;
    }
    public function redirect($path, $code)
    {

        /*
         if you're running in CLI (redirects cannot happen and thus shouldn't exit())
          or if your webserver is running PHP as a (F)CGI
           (it needs a previously set Status header to properly redirect)
         */
        if (strncmp('cli', PHP_SAPI, 3) !== 0)
        {
            if (headers_sent() !== TRUE)
            {
                if (strlen(session_id()) > 0) // if using sessions
                {
                    session_regenerate_id(TRUE); // avoids session fixation attacks
                    session_write_close(); // avoids having sessions lock other requests
                }

                if (strncmp('cgi', PHP_SAPI, 3) === 0)
                {
                    header(sprintf('Status: %03u', $code), TRUE, $code);
                }

                header('Location: '.$path, TRUE, (preg_match('~^30[1237]$~', $code) > 0) ? $code : 302);
            }

            exit();
        }
        /*
         301 - Moved Permanently
        302 - Found
        303 - See Other
        307 - Temporary Redirect (HTTP/1.1)
         */

        // TODO: is this needed?!
        # return new DisplayWith;
    }

    protected function view($view_name)
    {
        return new DisplayWith($view_name);
    }

    /**
     * Returns the equivalent of Apache's $_SERVER['REQUEST_URI'] variable.
     *
     * Because $_SERVER['REQUEST_URI'] is only available on Apache serves,
     * this equivalent is using other environment variables.
     */
    protected function uri()
    {
        if (isset($_SERVER['REQUEST_URI']))
        {
            $uri = $_SERVER['REQUEST_URI'];
        }
        else
        {
            if (isset($_SERVER['argv']))
            {
                $uri = $_SERVER['SCRIPT_NAME'].'?'.$_SERVER['argv'][0];
            }
            elseif (isset($_SERVER['QUERY_STRING']))
            {
                $uri = $_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING'];
            }
            else
            {
                $uri = $_SERVER['SCRIPT_NAME'];
            }
        }

        // prevent multiple slashes to avoid cross site requests via the Form API.
        return '/'.ltrim($uri, '/');
    }

}

class Controller extends Display
{
    //public $request;
    protected $DB;

    // internal datatype functional call
    protected static function parameters(array $parameters = array())
    {
        return new Functional_Caller($parameters);
    }

    public static function __callStatic($function, $arguments)
    {
        $class = get_called_class();

        // camel case is set to true - decamelize
        if (defined('CAMEL_CASE') && CAMEL_CASE == '1')
        {
            $function = fnCamelCase::_decamelize($function);
        }

        if (is_file(PARENT_DIRECTORY.'/library/functions/'.strtolower($class).'/'.$function.'.func.php'))
        {
            if (!function_exists($function))
            {
                require_once PARENT_DIRECTORY.'/library/functions/'.strtolower($class).'/'.$function.'.func.php';
            }
            // after the lowercase function is loaded - camelize the function for the class class

            $instance = new $class();

            if (count($arguments) > 0)
            {
                //return $instance->$function(implode(',', $arguments));
                return call_user_func_array(array($instance, $function), $arguments);
            }
            else
            {
                // requires PHP 5.4+
                return $instance->$function();
            }
        }
        else
        {
            // return FALSE;
            throw new Exception('Failed to load: /library/functions/'.strtolower($class).'/'.$function.'.func.php');
        }
    }

    protected function __construct()
    {
        load::service('DB');

        if (is_null($this->DB))
        {
            $this->DB = new DB;
        }
    }

    // in any controller: $this->installServices()
    protected function installServices()
    {
        // will parse the services.ini file and download any service folder not exiting
        // once finish function will return true else will return false
    }

    // in any controller: $this->deleteServices()
    protected function deleteServices()
    {
        // will parse the services.ini file and delete and services in the folder listed in the servies config file
        // once finish function will return true else will return false
    }


    /*
    * 
    * Check if a value is empty (...) if so then replaces with an empty string by default
    * or define set variable in the second parameter.
    */
    public function is_not_set($var, $default = TRUE)
    {
        return empty($var) ? $default : $var;
    }

    public function is_enum() {}     // objects (enumerate array)
    public function is_iterable($var) // arrays
    {
        return (bool) (is_array($var) || $var instanceof Traversable || $var instanceof stdClass);
    }

    /* Examples:
    -------------
    $this->isNotSet(Input::post('user_value1'));        // returns value if not empty
    $this->isNotSet(Input::post('user_value1'), 'N/A'); // returns N/A if empty
    */

}





