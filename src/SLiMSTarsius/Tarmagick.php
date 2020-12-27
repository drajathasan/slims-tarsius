<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2020-12-26 17:02:45
 * @modify date 2020-12-27 00:20:21
 */

namespace SLiMSTarsius;

// for production, comment if on development process
@ini_set('display_errors', false);
@error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);

class Tarmagick
{
    public static $parameter;
    public static $dir;
    public static $environment;

    public static function plugin($action)
    {
        self::getEnvironment(self::$dir);

        try {
            $Plugin = new \SLiMSTarsius\Plugin(self::$environment);

            if (method_exists($Plugin, $action))
            {
                $Plugin->$action(self::$dir, self::$parameter);
            }
            else
            {
                throw new \ErrorException($action);
            }
        } catch (\ErrorException $e) {
            \SLiMSTarsius\Docgenerator::failedMsg("Metode {pointMsg} tidak ada!", $e->getMessage());
        }
    }

    public static function module($action)
    {
        echo "Fitur akan hadir di masa mendatang :D, stay tune ya! \n";
    }

    public static function template($action)
    {
        echo "Fitur akan hadir di masa mendatang :D, stay tune ya! \n";
    }

    public static function library($action)
    {
        echo "Fitur akan hadir di masa mendatang :D, stay tune ya! \n";
    }

    public static function getEnvironment($dir)
    {
        if (file_exists($dir.'/sysconfig.inc.php'))
        {
            self::$environment = 'development';
        }
        else
        {
            self::$environment = 'development_src';
        }
    }

    public static function startup($mainDirectory)
    {
        $parser = (new \SLiMSTarsius\Parser())->compile();

        if (count($parser->arguments) > 0)
        {
            $method = $parser->arguments['method'][0];
            if (method_exists('SLiMSTarsius\\Tarmagick', $method))
            {
                self::$dir = $mainDirectory;
                self::$parameter = $parser->arguments['parameter'];
                return self::$method($parser->arguments['method'][1]);
            }
        }
        else
        {
            \SLiMSTarsius\Docgenerator::firstMeet();
        }
    }
}