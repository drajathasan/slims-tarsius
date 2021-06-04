<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2020-12-26 17:02:45
 * @modify date 2021-06-04 14:29:39
 */

namespace SLiMSTarsius;

use \SLiMSTarsius\Docgenerator as dg;

// require SLiMS Auto load
if (file_exists(DIR.'/sysconfig.inc.php') && file_exists(DIR.'/config/sysconfig.local.inc.php'))
{
    // set INDEX_AUTH
    define('INDEX_AUTH', '1');
    // set MWB take from sysconfig.inc.php
    $temp_senayan_web_root_dir = preg_replace('@admin.*@i', '', str_replace('\\', '/', dirname(@$_SERVER['PHP_SELF'])));
    define('SWB', $temp_senayan_web_root_dir.(preg_match('@\/$@i', $temp_senayan_web_root_dir)?'':'/'));
    define('MWB', SWB.'modules/');
    // load SLiMS database profile and autoload
    require DIR.'/config/sysconfig.local.inc.php';
    require DIR.'/lib/autoload.php';
}

class Module
{
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
        $interactiveMap = [
                           'module_uri' => 'Module URI (Alamat website)', 
                           'description' => 'Description', 
                           'version' => 'Version (Minimal gunakan semantic versioning)', 
                           'author' => 'Author (Pembuat)', 
                           'author_uri' => 'Author URI (Halaman profil pembuat)'
                          ];

        // set destination
        $destinantionDirectory = ($this->env === 'development_src')?$dest.'/tests/module/':$dest.'/admin/modules/';
        // setup template directory
        $templateDirectory = ($this->env === 'development_src')?$dest.'/tests/template/':$dest.'/vendor/drajat/slims-tarsius/tests/template/';

        if (count($parameter) > 1)
        {
            dg::failedMsg("{pointMsg}", 'Hanya bisa membuat 1 module dalam 1 perintah!');
        }

        $moduleName = $parameter[0];

        // set message
        echo "\nMembuat module \e[36m$moduleName\033[0m\n\n";
        // get information, create sampe data and make plugin
        $this->makeSampleData()
             ->makeInteractive($interactiveMap)
             ->makeModule($moduleName, $destinantionDirectory, $templateDirectory);
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
                // set error
                dg::failedMsg("Database : {pointMsg}", mysqli_connect_error());
            }

            // set criteria
            $criteria = '';
            if ($parameter[0] !== 'all')
            {
                $keyword  = $database->escape_string($parameter[0]);
                $criteria = " where module_name = '$keyword' OR  module_desc LIKE '%$keyword%'";
            }

            // get data
            $runQuery = $database->query('select * from mst_module'.$criteria);

            // check row
            if ($runQuery->num_rows > 0)
            {
                $listActive = [];
                $listDisctive = [];
                while ($data = $runQuery->fetch_assoc())
                {
                    if (file_exists(DIR.'/admin/modules/'.$data['module_path'].'/module.info.php'))
                    {   
                        include_once DIR.'/admin/modules/'.$data['module_path'].'/module.info.php';
                        // store into array
                        $moduleName = explode(' : ', $info[0][0]);
                        $listActive[] = [$moduleName[1]."\t\t", '/admin/modules/'.$data['module_path']];
                    }
                }
                // set list data
                $heading = " No Nama Module\t\t\t\t\tPath\t\n";
                dg::list('Berikut daftar module custom aktif', $listActive, $heading, 'module');
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
                // escape
                $keyword  = $database->escape_string($parameter[0]);
                $criteria = " where module_name = '$keyword' OR  module_desc LIKE '%$keyword%'";
            }

            // get data
            $runQuery = $database->query('select * from mst_module'.$criteria);

            if ($runQuery->num_rows > 0)
            {
                $info = [];
                while ($data = $runQuery->fetch_assoc())
                {
                    if (file_exists(DIR.'/admin/modules/'.$data['module_path'].'/module.info.php'))
                    {   
                        // include module info
                        include_once DIR.'/admin/modules/'.$data['module_path'].'/module.info.php';
                        // make detail
                        dg::info('Detail module custom '.$data['id'], $info);
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
                dg::failedMsg('Galat Database : {pointMsg} ', mysqli_connect_error());
            }
            // run query
            $database->query("CREATE TABLE IF NOT EXISTS `dummy_module` (
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
            @$database->query("INSERT IGNORE INTO `dummy_module` (`id`, `kolom1`, `kolom2`, `kolom3`) VALUES ('1', 'Test', 'Test', 'Test');");
        }
        // object
        return $this;
    }

    private function makeModuleData($data)
    {
        if (class_exists('\\SLiMS\\DB'))
        {
            // get database instance
            $database = \SLiMS\DB::getInstance('mysqli');
            // check connection
            if (mysqli_connect_error()) {
                dg::failedMsg('Galat Database : {pointMsg} ', mysqli_connect_error());
            }

            $valueQuery = [];
            foreach ($data as $value) {
                $valueQuery[] = $database->escape_string($value);
            }
            
            $filteredValue = '\''.implode('\', \'', $valueQuery).'\'';

            // run query
            $insertModule = $database->query("INSERT INTO `mst_module` (`module_name`, `module_path`, `module_desc`) VALUES ($filteredValue)");

            if ($insertModule)
            {
                $database->query("INSERT INTO `group_access` (`group_id`, `module_id`, `r`,`w`) VALUES (1, $database->insert_id, 1,1)");
            }
            
        }
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
                echo "\e[1m$question module?\033[0m [tuliskan] ";
                $this->interactiveResponse[$key] = trim(fgets(STDIN));
            }
            echo "\e[1mOtomatis aktifkan module?\033[0m [Y/n] ";
            $this->interactiveResponse['auto_active'] = trim(fgets(STDIN));

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
    private function makeModule(string $moduleName, string $destDir, string $templateDir)
    {
        // mpdify string
        $fixModuleName = ucwords(str_replace('_', ' ',trim($moduleName, '"\' ')));
        $dirModule = strtolower(str_replace(' ', '_', $fixModuleName));

        // check dir
        if (!is_dir($destDir.$dirModule))
        {
            // Make directory
            if (mkdir($destDir.$dirModule, 0755, true))
            {
                // get file template
                $indexModule = file_get_contents($templateDir.'index-module.Template');
                $submenuModule = file_get_contents($templateDir.'submenu.Template');
                $infoModule = file_get_contents($templateDir.'module-info.Template');
                // mutation
                $this->interactiveResponse['module_name'] = $fixModuleName;
                $this->interactiveResponse['path_name'] = $dirModule;
                $this->interactiveResponse['index_path'] = MWB.$dirModule.'/index.php';
                $this->interactiveResponse['date_created'] = date('Y-m-d H:i:s');

                foreach ($this->interactiveResponse as $key => $value) {
                    if (!empty(trim($this->interactiveResponse[$key])))
                    {
                        $indexModule = str_replace('{'.$key.'}', $value, $indexModule);
                        $submenuModule = str_replace('{'.$key.'}', $value, $submenuModule);
                        $infoModule = str_replace('{'.$key.'}', $value, $infoModule);
                    }
                    else
                    {
                        // remove directory
                        rmdir($destDir.$dirModule);
                        // set message
                        dg::failedMsg("Parameter {pointMsg} tidak boleh kosong!", $key);
                    }
                }

                try {
                    // set file
                    $indexModuleFile = file_put_contents($destDir.$dirModule.'/index.php', $indexModule);
                    $submenuModuleFile = file_put_contents($destDir.$dirModule.'/submenu.php', $submenuModule);
                    $authorFile = file_put_contents($destDir.$dirModule.'/module.info.php', $infoModule);

                    if ($indexModuleFile && $submenuModuleFile && $authorFile)
                    {
                        // set up module
                        $moduleData = [$dirModule,$dirModule, $this->interactiveResponse['description']];
                        if (strtolower($this->interactiveResponse['auto_active']) === 'y')  $this->makeModuleData($moduleData);

                        // set success message
                        dg::successMsg("{pointMsg}", "\nBerhasil membuat module $fixModuleName");
                    }
                } catch (\ErrorException $e) {
                    dg::failedMsg("Gagal membuat module {pointMsg} : $e->getMessage()", $fixModuleName);
                }
            }
            else
            {
                dg::failedMsg("{pointMsg}", "Gagal membuat direktori module");
            }
        }
        else
        {
            dg::failedMsg("{pointMsg}", "Module sudah ada. Ingin membuat module lagi? Hapus module yang sudah ada.");
        }
    }
}