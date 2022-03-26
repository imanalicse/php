<?php
if (isset($list_data) && !empty($list_data)) {
    foreach ($list_data as $data) {
        echo "<tr>";
        foreach ($data as $index => $item) {
            if (in_array($index, $listing_mapped_index)) {
                echo "<td> $item </td>";
            }
        }
        echo "</tr>";
    }
}