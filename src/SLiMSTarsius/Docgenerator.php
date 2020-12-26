<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2020-12-26 23:23:54
 * @modify date 2020-12-27 00:20:24
 * @desc [description]
 */

namespace SLiMSTarsius;

class Docgenerator
{
    public static function banner()
    {
echo " _____              _           
|_   _|_ _ _ __ ___(_)_   _ ___ 
  | |/ _` | '__/ __| | | | / __|
  | | (_| | |  \__ \ | |_| \__ \
  |_|\__,_|_|  |___/_|\__,_|___/                                            
";
                echo "\t\t\t v1.1.0 \n";
    }
    public static function firstMeet()
    {
        // Greeting
        self::banner();
        echo "\n Hai, ini tarsius perkakas pengembangan untuk \e[36mSLiMS \033[0m:D\n";
        // usage documentation
        self::usage();
    }

    public static function usage()
    {
        // Start doc
        echo "\n\e[33m Penggunaan \033[0m\n";
        echo "\n";
        echo "  php tarsius [perintah] [parameter]";
        echo "\n";
        echo "\n\e[33m Perintah Tersedia \033[0m\n";
        /* Plugin */
        echo "\n\e[36m  --plugin \033[0m";
        echo "\n\e[32m   --plugin:create \033[0m\tMembuat kerangka dasar plugin (mode non-hook)";
        echo "\n\e[32m   --plugin:enable \033[0m\tMengaktifkan plugin (fitur mendatang)";
        echo "\n\e[32m   --plugin:disable \033[0m\tMeng-non-aktifkan plugin (fitur mendatang)";
        echo "\n\e[32m   --plugin:delete \033[0m\tMenghapus plugin (fitur mendatang)";
        echo "\n\e[32m   --plugin:list \033[0m\tMenampilkan plugin tersedia (fitur mendatang)";
        echo "\n\e[32m   --plugin:info \033[0m\tMenampilkan informasi plugin (fitur mendatang)\n";
        /* Module */
        echo "\n\e[36m  --module \033[0m";
        echo "\n\e[32m   --module:create \033[0m\tMembuat kerangka dasar module (fitur mendatang)";
        echo "\n\e[32m   --module:list \033[0m\tMenampilkan module tersedia (fitur mendatang)";
        echo "\n\e[32m   --module:info \033[0m\tMenampilkan informasi module (fitur mendatang)\n";
        /* Template */
        echo "\n\e[36m  --template \033[0m\t(fitur mendatang)\n";
        /* Library */
        echo "\n\e[36m  --library \033[0m\t(fitur mendatang)\n";
        /* REST APi */
        echo "\n\e[36m  --rest-api \033[0m\t(fitur mendatang)\n";
        echo "\n\e[33m Contoh Penggunaan \033[0m\n";
        echo "\n";
        
        echo "  php tarsius --plugin:create buku_induk\n";
        echo "\n";
    }

    public static function failedMsg($message, $pointMessage)
    {
        die(str_replace("{pointMsg}", "\e[1m\e[31m$pointMessage\033[0m", "\n\e[1mError\033[0m:\n\n$message")."\n\n");
    }

    public static function successMsg($message, $pointMessage)
    {
        die(str_replace("{pointMsg}", "\e[1m\e[92m$pointMessage\033[0m", $message)."\n\n");
    }
}