<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2020-12-26 17:02:45
 * @modify date 2020-12-26 17:03:16
 */

namespace SLiMSTarsius;

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
                           'target_module' => 'Modul tujuan?',
                           'label_menu' => 'Teks yang muncul di Menu?'
                          ];

        $destinantion = ($this->env === 'development_src')?$dest.'/tests/plugins/':$dest.'/plugins/';
        $template = ($this->env === 'development_src')?$dest.'/tests/template/':$dest.'/template/';
        
        if (count(explode(' ', trim($pluginName))) > 1)
        {
            die('Hanya bisa membuat 1 plugin dalam 1 perintah!');
        }

        // set message
        echo 'Membuat plugin '.$pluginName."\n";
        // get information and make plugin
        $this->makeInteractive($interactiveMap)
             ->makePlugin($pluginName, $destinantion, $template);
    }

    private function makeInteractive($label)
    {
        if (is_array($label))
        {
            foreach ($label as $key => $question) {
                echo $question.' plugin? [tuliskan] ';
                $this->interactiveResponse[$key] = trim(fgets(STDIN));
            }

            return $this;
        }
        else
        {
            die('Label harus Array!');
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
                    $dotPlugin = str_replace('{'.$key.'}', $value, $dotPlugin);
                    $indexPlugin = str_replace('{'.$key.'}', $value, $indexPlugin);
                }

                try {
                    // set file
                    $dotPluginFIle = file_put_contents($destDir.$pluginName.'/'.strtolower(str_replace(' ', '_', $pluginName)).'.plugin.php', $dotPlugin);
                    $indexPluginFIle = file_put_contents($destDir.$pluginName.'/index.php', $indexPlugin);

                    if ($dotPlugin && $indexPlugin)
                    {
                        echo "Berhasil membuat plugin $pluginName \n";
                    }
                } catch (\ErrorException $e) {
                    die("Gagal membuat plugin $pluginName : \n ".$e->getMessage());
                }
            }
            else
            {
                die('Gagal membuat direktori plugin!');
            }
        }
        else
        {
            die('Plugin sudah ada. Ingin membuat plugin lagi? Hapus plugin yang sudah ada.'."\n");
        }
    }
}