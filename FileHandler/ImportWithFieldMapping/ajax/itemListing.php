<?php
require '../../../vendor/autoload.php';

use App\FileHandler\ImportWithFieldMapping\ImporterUtils;

$import_utils = new ImporterUtils();
$response = $import_utils->itemListing();
extract($response);
?>
<div class="mapping-table">
    <div class="table-responsive">
        <table class="table js-item-list-table">
            <thead>
                <tr>
                    <?php
                        if (isset($list_heading) && !empty($list_heading)) {
                            foreach ($list_heading as $index => $heading) {
                                if (in_array($index, $listing_mapped_index)) {
                                    echo "<td> $heading </td>";
                                }
                            }
                        }
                    ?>
                </tr>
            </thead>
            <tbody>
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
                ?>
            </tbody>
        </table>
    </div>
    <div class="paggination js-pagination-container">
        <input type="hidden" class="js-total-record" value="<?php echo $pagination_setting['total_record']; ?>">
        <input type="hidden" class="js-item-per-page" value="<?php echo $pagination_setting['item_per_page']; ?>">
        <input type="hidden" class="js-page" value="<?php echo $pagination_setting['page']; ?>">

        <div class="per-page">
            <span>Rows per page:</span>
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                  <span class="js-item-per-page-value"><?php echo $pagination_setting['item_per_page']; ?></span>
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu js-item-per-page-dropdown" aria-labelledby="dropdownMenu1">
                  <li><a href="#">10</a></li>
                  <li><a href="#">20</a></li>
                  <li><a href="#">30</a></li>
                  <li><a href="#">40</a></li>
                </ul>
              </div>
        </div>
        <div class="js-loader" style="display:none; text-align: Center;display: flex">
            Please Wait. <img src="../assets/img/ajax-loader.gif">
        </div>
        <div class="page-move">
            <span class="js-pagination-counter">1 - <?php echo $pagination_setting['item_per_page']; ?> of <?php echo $pagination_setting['total_record']; ?></span>

            <div class="pagination-arrows">
                <button class="btn js-prev">
                    <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 13L1.07071 7.07071C1.03166 7.03166 1.03166 6.96834 1.07071 6.92929L7 1" stroke="#222222" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
                <button class="btn js-next">
                    <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 13L6.92929 7.07071C6.96834 7.03166 6.96834 6.96834 6.92929 6.92929L1 1" stroke="#222222" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn brn-blue js-save-import-data">Save</button>
    </div>
    <div class="js-saving-loader" style="display:none; text-align: Center">
        Please Wait. <img src="../assets/img/ajax-loader.gif">
    </div>
    <div class="message js-message" style="text-align: center"></div>
</div>