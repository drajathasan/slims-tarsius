<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2020-12-26 17:02:45
 * @modify date 2020-12-27 00:20:27
 */

namespace SLiMSTarsius;

use \SLiMSTarsius\Docgenerator as dg;

class Plugin
{
    public $env;
    public $interactiveResponse;
    
    public function __construct($env)
    {
        @date_default_timezone_set('Asia/Jakarta');
        $this->env = $env;
    }

    public function create($dest, $pluginName)
    {
        $interactiveMap = ['plugin_uri' => 'Plugin URI (Alamat website)', 
                           'description' => 'Description', 
                           'version' => 'Version (Minimal gunakan semantic versioning)', 
                           'author' => 'Author (Pembuat)', 
                           'author_uri' => 'Author URI (Halaman profil pembuat)',
                           'target_module' => 'Modul tujuan',
                           'label_menu' => 'Teks yang muncul di Menu?'
                          ];

        $destinantion = ($this->env === 'development_src')?$dest.'/tests/plugins/':$dest.'/plugins/';
        $template = ($this->env === 'development_src')?$dest.'/tests/template/':$dest.'/vendor/drajat/slims-tarsius/tests/template/';
        
        if (count($pluginName) > 1)
        {
            dg::failedMsg("{pointMsg}", 'Hanya bisa membuat 1 plugin dalam 1 perintah!');
        }

        $pluginName = $pluginName[0];

        // set message
        echo "\nMembuat plugin \e[36m$pluginName\033[0m\n\n";
        // get information and make plugin
        $this->makeInteractive($interactiveMap)
             ->makePlugin($pluginName, $destinantion, $template);
    }

    private function makeInteractive($label)
    {
        if (is_array($label))
        {
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

    private function makePlugin($pluginName, $destDir, $template)
    {
        if (!is_dir($destDir.$pluginName))
        {
            if (mkdir($destDir.$pluginName, 0755, true))
            {
                // get file template
                $dotPlugin = file_get_contents($template.'dot-plugin.Template');
                $indexPlugin = file_get_contents($template.'index-plugin.Template');
                // mutation
                $this->interactiveResponse['plugin_name'] = strtolower(str_replace(' ', '_', $pluginName));
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
                        rmdir($destDir.$pluginName);
                        // set message
                        dg::failedMsg("Parameter {pointMsg} tidak boleh kosong!", $key);
                    }
                }

                try {
                    // set file
                    $dotPluginFIle = file_put_contents($destDir.$pluginName.'/'.strtolower(str_replace(' ', '_', $pluginName)).'.plugin.php', $dotPlugin);
                    $indexPluginFIle = file_put_contents($destDir.$pluginName.'/index.php', $indexPlugin);

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