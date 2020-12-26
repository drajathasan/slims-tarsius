<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2020-12-26 17:02:45
 * @modify date 2020-12-26 17:03:16
 */

namespace SLiMSTarsius;

class Tarmagick
{
    public static $parameter;
    public static $dir;
    public static $environment;

    public static function plugin($action)
    {
        self::getEnvironment(self::$dir);
        echo (new \SLiMSTarsius\Plugin(self::$environment))->$action(self::$dir, self::$parameter);
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
            else
            {
                echo "Metode tidak tersedia!";
            }
        }
        else
        {
            echo "Hai, ini tarsius perkakas pengemban untuk SLiMS :D \n";
        }
    }
}