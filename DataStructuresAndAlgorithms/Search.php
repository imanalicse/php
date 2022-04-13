<?php
namespace App\DataStructuresAndAlgorithms;

 class Search {
    /*
     * Time complexity: Worse case - O(n), Best case - O(1), Average case - O(n/2)
     */
    public static function LinearSearch(array $a, int $x): int {
        $count = count($a);
        for($i = 0; $i < $count; $i++) {
            if ($a[$i] == $x) {
                return $i;
            }
        }
        return -1;
    }
 }