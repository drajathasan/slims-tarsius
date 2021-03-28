<?php
/**
 * @author Drajat Hasan
 * @email [drajathasan20@gmail.com]
 * @create date 2021-03-28 10:34:09
 * @modify date 2021-03-28 10:34:09
 * @desc [description]
 */

namespace SLiMSTarsius;

use SLiMSTarsius\Migrationext\{AddAction};

class Migration
{
    private $type;
    private $optionsData;

    public function __construct()
    {

    }

    public function run()
    {
        $Options = [
            'kolom' => [
                'type' => 'varchar',
                'constraint' => 20,
                'null' => true,
                'default' => 'Pintar'
            ],
            'kolom2' => [
                'type' => 'int',
                'constraint' => 20,
                'null' => false,
                'default' => 10
            ]
        ];

        echo (AddAction::process('backup_log', AddAction::alter($Options)))."\n";
    }
}