<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2020-12-26 17:02:45
 * @modify date 2021-06-03 22:06:13
 */

namespace SLiMSTarsius;

use \SLiMSTarsius\Docgenerator as dg;

// require SLiMS Auto load
if (file_exists(DIR.'/sysconfig.inc.php') && file_exists(DIR.'/config/sysconfig.local.inc.php'))
{
    // set INDEX_AUTH
    define('INDEX_AUTH', '1');
    // load SLiMS database profile and autoload
    require DIR.'/config/sysconfig.local.inc.php';
    require DIR.'/lib/autoload.php';
}

class Plugin
{
    public $env;
    public $interactiveResponse;
    public $template = 'index-plugin';
    
    /**
     * Contructor
     *
     * @param string $env
     * @return	@return Contructor
     */
    public function __construct(string $env)
    {
        @date_default_timezone_set('Asia/Jakarta');
        $this->env = $env;
    }

    /**
     * Create Plugin
     *
     * @param string $dest
     * @param string $pluginName
     * @return void
     */
    public function create(string $dest, array $parameter)
    {
        // setup interactive question
        $interactiveMap = ['plugin_uri' => 'Plugin URI (Alamat website)', 
                           'description' => 'Description', 
                           'version' => 'Version (Minimal gunakan semantic versioning)', 
                           'author' => 'Author (Pembuat)', 
                           'author_uri' => 'Author URI (Halaman profil pembuat)',
                           'target_module' => 'Modul tujuan',
                           'label_menu' => 'Teks yang muncul di Menu?'
                          ];

        // set destination
        $destinantionDirectory = ($this->env === 'development_src')?$dest.'/tests/plugins/':$dest.'/plugins/';
        // setup template directory
        $templateDirectory = ($this->env === 'development_src')?$dest.'/tests/template/':$dest.'/vendor/drajat/slims-tarsius/tests/template/';
        
        // set up custom parameter
        if (isset($parameter[1]) && preg_match('/\--[a-z]+=/i', $parameter[1]))
        {
            $pattern = explode('=', trim($parameter[1], '-'));
            
            if (property_exists($this, $pattern[0]) && file_exists($templateDirectory.'/'.$pattern[1].'.Template'))
            {
                // set propperty
                $this->{$pattern[0]} = $pattern[1];
                // kill other parameter
                unset($parameter[1]);
            }
            else
            {
                dg::failedMsg("Template {pointMsg} tidak ada.", $pattern[1]);
            }
        }

        if (count($parameter) > 1)
        {
            dg::failedMsg("{pointMsg}", 'Hanya bisa membuat 1 plugin dalam 1 perintah!');
        }

        $pluginName = $parameter[0];

        // set message
        echo "\nMembuat plugin \e[36m$pluginName\033[0m\n\n";
        // get information, create sampe data and make plugin
        $this->makeSampleData()
             ->makeInteractive($interactiveMap)
             ->makePlugin($pluginName, $destinantionDirectory, $templateDirectory);
    }

    /**
     * List plugin
     *
     * @param string $dir
     * @param array $parameter
     * @return void
     */
    public function list(string $dir, array $parameter)
    {
        // class check
        if (class_exists('\\SLiMS\\DB'))
        {
            // get database instance
            $database = \SLiMS\DB::getInstance('mysqli');
            // check connection
            if (mysqli_connect_error()) {
                exit("\n");
            }

            // set criteria
            $criteria = '';
            if ($parameter[0] !== 'all')
            {
                $keyword  = $database->escape_string($parameter[0]);
                $criteria = " where id = '$keyword' OR path LIKE '%$keyword%'";
            }

            // get data
            $runQuery = $database->query('select * from plugins'.$criteria);

            // check row
            if ($runQuery->num_rows > 0)
            {
                $listActive = [];
                $listDisctive = [];
                while ($data = $runQuery->fetch_assoc())
                {
                    // check path
                    if (file_exists($data['path']))
                    {   
                        // slicing
                        $slicePath = explode('/', trim($data['path'], '/'));
                        // get plugin name
                        $plugin = isset($slicePath[count($slicePath) - 1])?$slicePath[count($slicePath) - 1]:'?';
                        unset($slicePath[count($slicePath) - 1]);
                        // store into array
                        $listActive[] = [$data['id'], '/'.implode('/', $slicePath).'/' ,$plugin];
                    }
                }
                // set list data
                $heading = " No ID\t\t\t\t\tNama Plugin\t\tPath\n";
                dg::list('Berikut daftar plugin aktif', $listActive, $heading);
                exit;
            }
            // set info
            dg::info('Info', [['TIdak ada data yang tersedia.']]);
            exit;
            
        }
        // set error
        dg::failedMsg("{pointMsg} tidak ada", 'Namespace \\SLiMS\\DB');
    }

    /**
     * Get Plugin Information
     *
     * @param string $dir
     * @param array $parameter
     * @return void
     */
    public function info(string $dir, array $parameter)
    {
        if (class_exists('\\SLiMS\\DB'))
        {
            // get database instance
            $database = \SLiMS\DB::getInstance('mysqli');
            // check connection
            if (mysqli_connect_errno()) {
                // set error
                dg::failedMsg("Database : {pointMsg}", mysqli_connect_error());
            }

            $criteria = '';
            if ($parameter[0] !== 'all')
            {
                $keyword  = $database->escape_string($parameter[0]);
                $criteria = " where id = '$keyword' OR path LIKE '%$keyword%'";
            }

            // get data
            $runQuery = $database->query('select * from plugins'.$criteria);

            if ($runQuery->num_rows > 0)
            {
                $info = [];
                while ($data = $runQuery->fetch_assoc())
                {
                    if (file_exists($data['path']))
                    {   
                        // parsing plugin data -> took from lib/Plugins.php
                        $file_open = fopen($data['path'], 'r');
                        $raw_data = fread($file_open, 8192);
                        fclose($file_open);

                        preg_match('|Plugin Name:(.*)$|mi', $raw_data, $info[0]);
                        preg_match('|Plugin URI:(.*)$|mi', $raw_data, $info[1]);
                        preg_match('|Version:(.*)|i', $raw_data, $info[2]);
                        preg_match('|Description:(.*)$|mi', $raw_data, $info[3]);
                        preg_match('|Author:(.*)$|mi', $raw_data, $info[4]);
                        preg_match('|Author URI:(.*)$|mi', $raw_data, $info[5]);
                        // make detail
                        dg::info('Detail plugin '.$data['id'], $info);
                    }
                }
                exit;
            }
            // set info
            dg::info('Info', [['TIdak ada data yang tersedia.']]);
            exit;
        }
        // set error
        dg::failedMsg("{pointMsg} tidak ada", 'Namespace \\SLiMS\\DB');
        exit;
    }


    /**
     * Making Sample Data
     *
     * @return object
     */
    private function makeSampleData()
    {
        if (class_exists('\\SLiMS\\DB'))
        {
            // get database instance
            $database = \SLiMS\DB::getInstance('mysqli');
            // check connection
            if (mysqli_connect_error()) {
                exit("\n");
            }
            // run query
            $database->query("CREATE TABLE IF NOT EXISTS `dummy_plugin` (
                        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY, 
                        `kolom1` varchar(20) NULL,
                        `kolom2` varchar(20) NULL,
                        `kolom3` varchar(20) NULL
                      ) ENGINE='MyISAM';");

            // check
            if ($database->error)
            {
                dg::failedMsg('Gagal membuat {pointMsg} karena : '.$database->error, 'SampleData');
            }
            // insert data
            @$database->query("INSERT IGNORE INTO `dummy_plugin` (`id`, `kolom1`, `kolom2`, `kolom3`) VALUES ('1', 'Test', 'Test', 'Test');");
        }
        // object
        return $this;
    }

    /**
     * Interactive Question
     *
     * @param array $label
     * @return void
     */
    private function makeInteractive(array $label)
    {
        // check label
        if (is_array($label))
        {
            // loop
            foreach ($label as $key => $question) {
                echo "\e[1m$question plugin?\033[0m [tuliskan] ";
                $this->interactiveResponse[$key] = trim(fgets(STDIN));
            }

            return $this;
        }
        else
        {
            dg::failedMsg("{pointMsg} harus Array!", 'Label');
        }
    }

    /**
     * Make plugin
     *
     * @param string $pluginName
     * @param string $destDir
     * @param string $template
     * @return void
     */
    private function makePlugin(string $pluginName, string $destDir, string $templateDir)
    {
        // mpdify string
        $fixPluginName = ucwords(str_replace('_', ' ',trim($pluginName, '"\' ')));
        $dirPlugin = strtolower(str_replace(' ', '_', $fixPluginName));

        // check dir
        if (!is_dir($destDir.$dirPlugin))
        {
            // Make directory
            if (mkdir($destDir.$dirPlugin, 0755, true))
            {
                // get file template
                $dotPlugin = file_get_contents($templateDir.'dot-plugin.Template');
                $indexPlugin = file_get_contents($templateDir.$this->template.'.Template');
                // mutation
                $this->interactiveResponse['plugin_name'] = $fixPluginName;
                $this->interactiveResponse['date_created'] = date('Y-m-d H:i:s');

                foreach ($this->interactiveResponse as $key => $value) {
                    if (!empty(trim($this->interactiveResponse[$key])))
                    {
                        $dotPlugin = str_replace('{'.$key.'}', $value, $dotPlugin);
                        $indexPlugin = str_replace('{'.$key.'}', $value, $indexPlugin);
                    }
                    else
                    {
                        // remove directory
                        rmdir($destDir.$dirPlugin);
                        // set message
                        dg::failedMsg("Parameter {pointMsg} tidak boleh kosong!", $key);
                    }
                }

                try {
                    // set file
                    $dotPluginFIle = file_put_contents($destDir.$dirPlugin.'/'.strtolower(str_replace(' ', '_', $pluginName)).'.plugin.php', $dotPlugin);
                    $indexPluginFIle = file_put_contents($destDir.$dirPlugin.'/index.php', $indexPlugin);

                    if ($dotPlugin && $indexPlugin)
                    {
                        dg::successMsg("{pointMsg}", "\nBerhasil membuat plugin $pluginName");
                    }
                } catch (\ErrorException $e) {
                    dg::failedMsg("Gagal membuat plugin {pointMsg} : $e->getMessage()", $pluginName);
                }
            }
            else
            {
                dg::failedMsg("{pointMsg}", "Gagal membuat direktori plugin");
            }
        }
        else
        {
            dg::failedMsg("{pointMsg}", "Plugin sudah ada. Ingin membuat plugin lagi? Hapus plugin yang sudah ada.");
        }
    }
}