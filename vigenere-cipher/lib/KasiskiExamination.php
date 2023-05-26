<?php

require_once './lib/Helper.php';
require_once './lib/CaesarCipher.php';

class KasiskiExamination
{
    // use Helper;

    private $ciphertext;
    
    public $explanation;

    public $indexs_name = [
        '0' => 'first',
        '1' => 'second',
        '2' => 'third',
        '3' => 'fourth',
        '4' => 'fifth',
        '5' => 'sixth',
        '6' => 'seventh',
    ];

    /**
     * Preparing the ciphertext.
     */
    function __construct($ciphertext)
    {
        $this->explanation['ciphertextparam'] = $ciphertext;

        $ciphertext = trim($ciphertext);
        $ciphertext = preg_replace("/\s+/", "", $ciphertext);
        $ciphertext = str_replace('.', '', $ciphertext);

        $this->ciphertext = strtoupper($ciphertext);
        $this->explanation['ciphertext'] = $this->ciphertext;
    }
    
    /**
     * The first part of Kasiski Examination is to find every repeated set of letters at least three letters long in the ciphertext.
     * These are significant, because they could indicate that they were the same letters of plaintext encrypted with the same subkeys of the key.
     * 
     * @param int $minLength.
     * @param int $maxLength.
     * @return array $sequences.
     */
    public function findRepeatSequences($minLength, $maxLengths)
    {
        $sequences = [];
        
        for ($i = 0; $i < strlen($this->ciphertext) - $minLength; $i++)
        {
            foreach ($maxLengths as $maxLength)
            {
                $sub = substr($this->ciphertext, $i, $maxLength);
                if (preg_match_all("/$sub/", $this->ciphertext, $matches)) {
                    if(count($matches[0]) >= 2) {
                        foreach ($matches[0] as $match) 
                        {
                            if (!in_array($match, $sequences) ) {
                                $sequences[] = $match;
                            }
                        }
                    }
                }
            }
        }
        
        $sequences = array_filter(array_unique($sequences));
        
        foreach ($sequences as $sequence)
        {
            $this->explanation['sequencesexplain'][$sequence] = '';
            
            $splits = explode($sequence, $this->ciphertext);
            foreach($splits as $key => $str) {
                if (strpos($this->ciphertext, $sequence) == 0 && $key == 0)
                    $this->explanation['sequencesexplain'][$sequence] .= "<b><u>".$sequence."</u></b>".$str;
                elseif (strpos($this->ciphertext, $sequence) != 0 && $key != 0)
                    $this->explanation['sequencesexplain'][$sequence] .= "<b><u>".$sequence."</u></b>".$str;
                else
                    $this->explanation['sequencesexplain'][$sequence] .= $str;
            }
        }

        $this->explanation['sequences'] = $sequences;
        return $sequences;
    }

    /**
     * Calculate the distances between repeated sequences and store them in an array.
     * 
     * @param array $sequences.
     * @return array $distances.
     */
    public function getDistances($sequences)
    {
        $distances = array();
        $infos = array();
        
        foreach ($sequences as $sequence) {
            // get the sequence position on cipher.
            $index = strpos($this->ciphertext, $sequence);

            // remove the string before the sequence in cipher.
            $result = substr($this->ciphertext, $index + strlen($sequence));
            $splits = explode($sequence, $result);

            // Remove the last part from string after the last sequence.
            $count_of_splits = count($splits);
            unset($splits[$count_of_splits-1]);

            // Looping on splits parts to get explain info.
            foreach($splits as $key => $str) {
                $length = strlen($sequence.''.$str);
                $splits[$key] = $length;
                array_push($infos, "Between the ".$this->indexs_name[$key]." and " . $this->indexs_name[$key+1] . " " .$sequence." there are " . $length . " letters");
                
                // Calculate the distances between first and other repeated
                $parts = count($splits);
                $partmapping = $parts-1;
                if($parts > 1 && $key == ($parts-1)) {
                    for ($i=$parts; $i < ($parts + $partmapping); $i++) {
                        $length = $splits[0] + $splits[$i- $partmapping];
                        $splits[$i] = $length;
                        array_push($infos, "Between the ".$this->indexs_name[0]." and " . $this->indexs_name[($i+1)- $partmapping] . " " .$sequence." there are " . $length . " letters");
                    }
                }
            }
            // Assign splits for the sequence.
            $distances[$sequence] = $splits;
            $this->explanation['distances'][$sequence] = $splits;
        }

        $this->explanation['distancesexplain'] = $infos;

        return $distances;
    }

    /**
     * Get Factors of the sequences distances.
     * 
     * @param array $distances.
     * @return array $factoriesTimes.
     */
    public function getFactories($distances)
    {
        $factories = array();
        $factoriesTimes = array();
        $unique_numbers = array();
        $helper = new Helper();
        
        foreach ($distances as $seq => $dists) {
            // get the Factories for each distance value. 
            foreach ($dists as $value) {
                if(! in_array($value, $unique_numbers)) {
                    // get all Factories for the value. 
                    $result = $helper->getNumberFactories($value);
                    
                    // assign items only from array to list of factories.
                    foreach ($result as $fac_value) {
                        array_push($factories, $fac_value);
                        // Get Factories Times.
                        if(isset($factoriesTimes[$fac_value])) {
                            $factoriesTimes[$fac_value] = $factoriesTimes[$fac_value] + 1;
                        } else {
                            $factoriesTimes[$fac_value] = 1;
                        }
                    }
                    // Add explain.
                    $this->explanation['factoriesexplain'][$seq][$value] = $result;

                    // Push the number in array.
                    array_push($unique_numbers, $value);
                }
            }
        }

        $this->explanation['factories'] = $factories;
        $this->explanation['factories_numbers'] = $unique_numbers;
        $this->explanation['factoriestimes'] = $factoriesTimes;

        return $factoriesTimes;
    }

    /**
     * The keys that have the highest factories count are the most likely lengths of the Vigenère key.
     * @param array $factories.
     * @param array $keys.
     */
    public function getKeyLength($factories)
    {
        $maxVal = max($factories);
        $keys = [];
        foreach ($factories as $key => $value) {
            if ($value == $maxVal) {
                array_push($keys, $key);
            }  
        }
        // assign explain
        $this->explanation['keyslengthexplain'] = $keys;

        return $keys;
    }

    /**
     * Get Every Nth Letters. we will want to split up the ciphertext into every (n)th letter.
     * 
     * @param int $keylength.
     * @return array $strings.
     */
    public function getNthLetters($keylength)
    {
        $strings = [];
        $this->explanation['nthlettersexplain'] = [];
        $letters_splits = str_split($this->ciphertext);
        
        for ($x = 0; $x < $keylength; $x++)
        {
            $string = '';
            for ($i = $x; $i < strlen($this->ciphertext); $i += $keylength) {
                $string .= $letters_splits[$i];
            }

            $this->explanation['nthlettersexplain'][] = "Every ".$keylength."<sup>th</sup> letter starting with the ".$this->indexs_name[$x]." letter: <b>".$string."</b>";
            $strings[] = $string;
        }
        
        $this->explanation['nthletters'] = $strings;
        
        return $strings;
    }

    /**
     * Letter frequency is the number of times letters of the alphabet appear on average in written language.
     * 
     * @param array $Nthstrings
     * @return array $frequency
     */
    public function frequencyAnalysis($Nthstrings)
    {
        $frequencies = $highests = [];

        $caesar = new CaesarCipher();
        $helper = new Helper();

        // Loop for the strings to get the frequency analysis to each one.
        foreach ($Nthstrings as $string) {
            $frequency = $caesar->crackingBruteForceAttack($string, 1, 1);

            $frequencies[$string] = $frequency;
            $this->explanation['frequencies'][$string] = $frequency;
        }

        // Loop for each string scores to get the highest letter score.
        foreach ($frequencies as $string => $list)
        {
            $scores = [];
            foreach ($list as $row) {
                $scores[$row['subkey']] = $row['score'];
            }
            // Get Max Scores
            $highest = $helper->getMaxSubkeys($scores);
            $highests[$string] = $highest;
            $this->explanation['highestfrequencies'][$string] = $highest;
        }

        return $highests;
    }

    /**
     * we will brute-force the key by trying out every combination of subkey.
     * 
     * @param array $subkeys
     * @return array $keys
     */
    public function getPossibleKeys($subkeys)
    {
        $subkeys = array_values($subkeys);
        $keys = [];
        $combinations = [[]];

        foreach ($subkeys as $letters) {
            // Build Combinations arrays for each letters.
            $newCombinations = [];
            foreach ($combinations as $combination) {
                // Build array for each letter on combination array (first one)
                foreach ($letters as $letter) {
                    $newCombinations[] = array_merge($combination, [$letter]);
                }
            }
            // add to main array.
            $combinations = $newCombinations;
        }
    
        // Convert combinations associative array to array of strings 
        foreach ($combinations as $combination) {
            array_push($keys, implode('', $combination));
        }
        
        $this->explanation['possibleKeys'] = $keys;
        return $keys;
    }

    /**
     * We will check the keys list to get the valid world in english.
     * 
     * @param array $keys
     * @return array $validWords
     */
    public function checkKeysValidEnglish($keys)
    {
        $helper = new Helper();
        $result = $helper->checkValidWords($keys);
        $this->explanation['validkeys'] = $result;
        
        return $result;
    }
}
