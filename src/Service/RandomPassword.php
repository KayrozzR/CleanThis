<?php

namespace App\Service;

class RandomPassword
{
    function genererMotDePasse (int $longuermdp):mixed{
        $min = "abcdefghijklmnopqrstuvwxyz";
        $maj ="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $nmbre = "0123456789";

        //first we shuffle the lowercase, uppercase and number
        $shuffle1 = str_shuffle($min);
        $shuffle2 = str_shuffle($maj);
        $shuffle3 = str_shuffle($nmbre);
        $specialChars = '!@#$%^&*()_-+=<>?';

        //assemble the lowercase, uppercase and number
        $shuffled = $shuffle1 .$shuffle2 .$shuffle3. $specialChars ;
        //we shuffle everything 
        $psword = str_shuffle($shuffled);
        //we return the psword substring with the number of characteres that we have put in the function parameter
        $passWord = substr($psword,0,$longuermdp);
        return $passWord; 
    }
}



