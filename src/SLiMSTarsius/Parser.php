<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2020-12-26 17:02:45
 * @modify date 2020-12-26 17:03:34
 */

namespace SLiMSTarsius;

class Parser
{
    public $arguments;

    private function getArguments()
    {
        unset($_SERVER['argv'][0]);
        $this->arguments = $_SERVER['argv'];
    }

    public function eachArgument($position = '')
    {
        $this->getArguments();
        return ($this->arguments[$position])??$this->arguments[$position];
    }

    public function compile()
    {
        $this->getArguments();
        $map = [1 => 'method', 'parameter'];

        foreach ($this->arguments as $id => $value) {
            if ($id > 1)
            {
                $this->arguments[$map[2]][] =  $value;
            }
            else
            {
                $value = explode(':', $value);
                if (isset($value[0]) && isset($value[1])) 
                {
                    $this->arguments[$map[$id]] =  [str_replace('-', '', $value[0]), $value[1]];
                }
                else
                {
                    \SLiMSTarsius\Docgenerator::firstMeet(); exit;
                }
            }
            unset($this->arguments[$id]);
        }

        return $this;
    }
}