<?php
namespace App\DataStructuresAndAlgorithms;

 class Search {
    /*
     * Time complexity: Worse case - O(n), Best case - O(1), Average case - O(n/2)
     */
    public static function linearSearch(array $a, int $x): int {
        $count = count($a);
        for($i = 0; $i < $count; $i++) {
            if ($a[$i] == $x) {
                return $i;
            }
        }
        return -1;
    }

    /*
     * Items must be sorted
     * Time complexity: Ο(log n)
     * TODO have to implement with Recursive method
     */

    public static function binarySearch(array $arr, int $search_item): int {
        $total = count($arr);
        $left_index = 0;
        $right_index = $total - 1;
        while ($left_index <= $right_index) {
            $mid_index = floor(($left_index + $right_index) / 2);
            if ($arr[$mid_index] == $search_item)
                return $mid_index;

            if ($arr[$mid_index] < $search_item)
                $left_index = $mid_index + 1;
            else
                $right_index = $mid_index - 1;
        }
        return -1;
    }
 }

$data = [1, 5, 12, 15, 20, 45, 46, 48, 50];
$index = Search::binarySearch($data, 48);
echo "<pre>";
print_r($index);
echo "</pre>";