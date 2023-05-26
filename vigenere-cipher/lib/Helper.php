<?php

class Helper
{
    public function getNumberFactories($number)
    {
        $list = [];
    
        for($i=1; $i <= sqrt($number); $i++) {
            if($number % $i == 0) {
                if($i > 1) {
                    $list[] = $i;
                }
            }
            
            //handing perfect squares
            if($number / $i == $i) {
                $i--;
                break;
            }
        }
    
        for($i; $i >= 1; $i--) {
            if($number % $i == 0) {
                $div = $number / $i;
                if($div > 1) {
                    $list[] = $div;
                }
            }
        }
    
        return array_filter($list);
    }
    

    public function getMaxSubkeys($list)
    {
        $maxVal = max($list);
        $values = [];
        foreach ($list as $letter => $value) {
            if ($value == $maxVal) {
                array_push($values, $letter);
            }  
        }

        return $values;
    }
    

    public function checkValidWords($words)
    {
        $valids = [];
        $pspell = pspell_new("en");
        foreach ($words as $word) {
            if (pspell_check($pspell, $word)) {
                $valids[] = $word;
            }
        }
    
        return $valids;
    }
}

