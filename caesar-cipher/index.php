<?php

require_once './lib/CaesarCipher.php';

// Get instance object 
$caesar = new CaesarCipher();

echo "<pre>";

// Add your plaintext
$plaintext = "java t point";
// Call the encryptMessage funciton and pass plaintext and keylength
$ciphertext = $caesar->encryptMessage($plaintext, 3);
echo $ciphertext;
echo "<br><hr><br>";

// Call the decryptMessage funciton and pass ciphertext and keylength
$plaintext = $caesar->decryptMessage($ciphertext, 3);
echo $plaintext;
echo "<br><hr><br>";

// To cracking the ciphertext call the crackingBruteForceAttack funciton and pass ciphertext.
$cracktext = $caesar->crackingBruteForceAttack($ciphertext);
print_r($cracktext);

// This to print all code explanation.
echo "<br><hr><br>";
print_r($caesar->explanation);