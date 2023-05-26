<?php

require_once './lib/FrequencyAnalysis.php';

/**
 * Caesar Cipher Algorithm.
 * 
 * In this technique, each letter of the given text is replaced by a letter of some fixed number of positions down the alphabet.
 * An integer value is required to cipher a given text. The integer value is 
 * known as shift, which indicates the number of positions each letter of
 * the text has been moved from our alphabetical.
 */
class CaesarCipher
{
    /**
     * Our Alphabetical.
     */
    protected $alphabetical = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
    ];

    public $explanation;


    private function preparingText($text)
    {
        $this->explanation['text'] = $text;
        $text = trim($text);
        $text = preg_replace("/\s+/", "", $text);
        $text = str_replace('.', '', $text);
        $text = strtoupper($text);
        $this->explanation['caesartext'] = $text;
        
        return $text;
    }

    /**
     * En(X) = (x + n) mod 26
     */
    public function encryptMessage(string $plainText, int $key)
    {
        $plainText = $this->preparingText($plainText);
        $cipherText = '';
        $explain = []; 
        
        for ($i = 0; $i < strlen($plainText); $i++)
        {
            $position = array_search($plainText[$i], $this->alphabetical);
            $enPosition = ($position + $key) % count($this->alphabetical);
            $cipherChar = $this->alphabetical[$enPosition];
            $cipherText .= $cipherChar;

            $explain[][$plainText[$i]] = '<div> Plain: ' . $plainText[$i] . ' --> ' . $position . ' </div> <div> En: ('.$position.'+'.$key.') mod 26 </div>  <div> Cipher: ' . $enPosition .' --> '.$cipherChar.' </div>';
        }
        
        $this->explanation['ciphertext'] = $cipherText;
        $this->explanation['explain'] = $explain;

        return $cipherText;
    }
    
    /**
     * Dn(X) = (Xi - n) mode 26;
     */
    public function decryptMessage(string $cipherText, int $key, int $isPrepared = 0)
    {
        $cipherText = (!$isPrepared) ? $this->preparingText($cipherText) : $cipherText;
        $plainText = '';
        $explain = []; 
        
        for ($i = 0; $i < strlen($cipherText); $i++)
        {
            $position = array_search($cipherText[$i], $this->alphabetical);
            $dePosition = ($position - $key) % count($this->alphabetical);
            if($dePosition < 0) {
                $dePosition = $dePosition + count($this->alphabetical);
            }
            $plainChar = $this->alphabetical[$dePosition];
            $plainText .= $plainChar;

            $explain[][$cipherText[$i]] = '<div> Cipher: ' . $cipherText[$i] . ' --> ' . $position . ' </div> <div> En: ('.$position.'-'.$key.') mod 26 </div>  <div> Plain: ' . $dePosition .' --> '.$plainChar.' </div>';
        }

        $this->explanation['plainText'] = $plainText;
        $this->explanation['explain'] = $explain;

        return $plainText;
    }

    /**
     * Cracking Caesar cipher.
     * 
     * Useing (Brute-Force Attack) because the number of possible key is 26,
     * thats why we can consider all these cases (so check all the possible key values).
     */
    public function crackingBruteForceAttack(string $cipherText, int $isPrepared = 0, int $score = 0) 
    {
        $cipherText = (!$isPrepared) ? $this->preparingText($cipherText) : $cipherText;
        $text = [];
        $frequency = new FrequencyAnalysis();

        for ($shift = 0; $shift < count($this->alphabetical); $shift++)
        {
            $decryptedText = $this->decryptMessage($cipherText, $shift, 1);
            if($score) {
                $text[] =  [
                    'subkey' => $this->alphabetical[$shift],
                    'shift' => $shift,
                    'decryptedText' => $decryptedText,
                    'score' => $frequency->englishFreqMatchScore($decryptedText),
                ];
            } else {
                $textRaw =  [
                    'shift' => $shift,
                    'decryptedText' => $decryptedText,
                ];
                $text[] = $textRaw;
                
                $this->explanation['explainhacking'][] = $textRaw;
            }
        }

        return $text;
    }

}
