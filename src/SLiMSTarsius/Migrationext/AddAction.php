<?php

namespace SLiMSTarsius\Migrationext;

class AddAction
{
    public static function alter($arrayData)
    {
        $rawQuery = [];
        foreach ($arrayData as $column => $options) {
            $rawQuery[$column] = [];
            $rawQuery[$column][0] = 'ADD `' . $column . '` ';
            foreach ($options as $option => $value) {
                switch ($option) {
                    case 'type':
                        $withContraint = '';
                        if ((isset($options['constraint']) && !in_array($options['type'], ['datetime','text','longtext','mediumtext',''])))
                        {
                            $withContraint = '(' .(int)$options['constraint']. ')';
                        }
                        $rawQuery[$column][1] = str_replace("'", "\'", $value) . $withContraint.' ';
                        break;
                    case 'null':
                        $rawQuery[$column][2] = 'NOT NULL ';
                        if ($value)
                        {
                            $rawQuery[$column][2] = 'NULL ';
                        }
                    case 'default':
                        $rawQuery[$column][3] = 'DEFAULT \'' . str_replace("'", "\'", $value) . '\' ';
                        break;
                    case 'increment':
                        $rawQuery[$column][4] = 'AUTO_INCREMENT UNIQUE ';
                        break; 
                    case 'after':
                        $rawQuery[$column][5] = 'AFTER `' . str_replace("'", "\'", $value) .'` ';
                        break;
                }
            }
        }

        return ['ALTER TABLE ', $rawQuery];
    }

    public static function process($tableName, $raw)
    {
        $rawQuery = $raw[0] . ' `'. $tableName .'` ';
        foreach ($raw[1] as $value) {
            $rawQuery .= trim(implode('', $value)). ', ';
        }

        $rawQuery = substr_replace($rawQuery, ';', -2);

        echo $rawQuery;
    }
}