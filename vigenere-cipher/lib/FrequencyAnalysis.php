<?php

/**
 * Frequency Analysis Class.
 * 
 * Letter frequency is the number of times letters of the alphabet appear on average in written language.
 * frequency taken from http://en.wikipedia.org/wiki/Letter_frequency
 */
class FrequencyAnalysis
{
    /**
     * Our Alphabetical.
     * ABCDEFGHIJKLMNOPQRSTUVWXYZ
     */
    protected $alphabetical = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
    ];

    /**
     * Our Alphabetical sorting depend on the English Letter frequency.
     * ETAOINSHRDLCUMWFGYPBVKJXQZ
     */
    protected $mostFrequentAlphabetical = [
        'E', 'T', 'A', 'O', 'I', 'N', 'S', 'H', 'R', 'D', 'L', 'C', 'U', 'M', 'W', 'F', 'G', 'Y', 'P', 'B', 'V', 'K', 'J', 'X', 'Q', 'Z'
    ];

    /**
     * This is the most frequents letter.
     */
    protected $mostLetters = [
        'E', 'T', 'A', 'O', 'I', 'N' // ETAOIN
    ];
    
    /**
     * This is the most leatest letter.
     */
    protected $leastLetters = [
        'V', 'K', 'J', 'X', 'Q', 'Z' // VKJXQZ
    ];

    /**
     * Returns a array with keys of single letters and values of the
     * count of how many times they appear in the message parameter.
     */
    public function getLetterCount($message)
    {
        $letterCount = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0, 'F' => 0, 'G' => 0, 'H' => 0, 'I' => 0, 'J' => 0, 'K' => 0, 'L' => 0, 'M' => 0, 'N' => 0, 'O' => 0, 'P' => 0, 'Q' => 0, 'R' => 0, 'S' => 0, 'T' => 0, 'U' => 0, 'V' => 0, 'W' => 0, 'X' => 0, 'Y' => 0, 'Z' => 0];
        
        $message = strtoupper($message);

        for ($i = 0; $i < strlen($message); $i++) {
            if(in_array($message[$i], $this->alphabetical) ) {
                $letterCount[$message[$i]] +=1;
            }
        }

        return $letterCount;
    }

    /**
     * Returns a string of the alphabet letters arranged in order of most
     * frequently occurring in the message parameter.
     */
    public function getFrequencyOrder($message)
    {
        // first, get a array of each letter and its frequency count
        $letterToFreq = $this->getLetterCount($message);

        // second, make a array of each frequency count to each letter(s) with that frequency
        $freqToLetter = [];
        foreach ($this->alphabetical as $key => $value) {
            $freqToLetter[$letterToFreq[$value]][] = $value;
        }
        
        // third, put each array of letters in reverse "ETAOIN" order, and then convert it to a string
        foreach ($freqToLetter as $count => $letters) {
            $ordering = [];
            foreach ($letters as $index => $letter) {
                $ordering[array_search($letter, $this->mostFrequentAlphabetical)] = $letter;
            }

            $freqToLetter[$count] = $ordering;
            krsort($freqToLetter[$count]);
            $freqToLetter[$count] = implode('', $freqToLetter[$count]);
        }

        // fourth, reverse the freqToLetter array depend on count (key)
        krsort($freqToLetter);
        
        $letterOrder = implode('', $freqToLetter);

        return $letterOrder;
    }


    /**
     * Return the number of matches that the string in the message 
     * parameter has when its letter frequency is compared to English letter frequency.
     */
    public function englishFreqMatchScore($message)
    {
        $freqOrder = $this->getFrequencyOrder($message);
        $mostCommon = substr($freqOrder, 0, 6);
        $leastCommon = substr($freqOrder, -6);
        
        $matchScore = 0;
        # Find how many matches for the six most common letters there are.
        foreach ($this->mostLetters as $commonLetter) {
            if(strpos($mostCommon, $commonLetter) !== false) {
                $matchScore++;
            }
        }

        # Find how many matches for the six least common letters there are.
        foreach ($this->leastLetters as $uncommonLetter) {
            if(strpos($leastCommon, $uncommonLetter) !== false) {
                $matchScore++;
            }
        }

        return $matchScore;
    }
    
}


