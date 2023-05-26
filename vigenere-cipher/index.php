<?php

require_once './lib/KasiskiExamination.php';

// Add your ciphertext 
$cipher = "Ppqca xqvekg ybnkmazu ybngbal jon i tszm jyim. Vrag voht vrau c tksg. Ddwuo xitlazu vavv raz c vkb qp iwpou.";
// $cipher = "Ppqca xqvekg ybnkmazu ybngbal jon i tszm jyim. Vrag voht vrau c tksg. Ddwuo xitlazu vavv raz c vkb qp iwpou. v rab ivwz vp ojpmu.";

// Get the instance object.
$kasiski = new KasiskiExamination($cipher);

// Call the findRepeatSequences function to get repeated letters.
$sequences = $kasiski->findRepeatSequences(3, [3, 4, 5]);

// Call the getDistances function to get the distances between repeated letters.
$distances = $kasiski->getDistances($sequences);

// Call the getFactories function to get the factories of distances.
$factories = $kasiski->getFactories($distances);

// Call the getKeyLength function to get the possible keyLength of factories.
$keyLength = $kasiski->getKeyLength($factories);


// Call the getNthLetters function to get the number of the letter encypted text.
// If the $keyLength have more then one value, will guess the key length from values was returned. 
$NthLetters = $kasiski->getNthLetters(4);

// Call the frequencyAnalysis function to get the all possible letters.
$possibleSubkeys = $kasiski->frequencyAnalysis($NthLetters);

// Call the getPossibleKeys function to get the possible keys.
$keys = $kasiski->getPossibleKeys($possibleSubkeys);

// Call the checkKeysValidEnglish function to get the valid english word..
$worlds = $kasiski->checkKeysValidEnglish($keys);

// Print all code explanation.
echo "<pre>";
print_r($kasiski->explanation);
echo "<br><br>";