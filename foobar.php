<?php

for ($i = 1; $i <= 100; $i++) {
    //Where the number is divisible by three (3) and (5) output the word “foobar”
    if ($i % 3 == 0 && $i % 5 == 0) {
        echo "foobar \t";
    } elseif ($i % 3 == 0 || $i % 5 == 0) {
        //Where the number is divisible by three (3) output the word “foo”
        if ($i % 3 == 0) {
            echo "foo \t";
        }//Where the number is divisible by five (5) output the word “bar”
        elseif ($i % 5 == 0) {
            echo "bar \t";
        }
    } else {
        //Output the numbers from 1 to 100 
        echo $i . "\t";
    }
}
?>