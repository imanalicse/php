<?php
require '../../../vendor/autoload.php';

use App\ImportExport\ImportWithFieldMapping\ImporterUtils;

$import_utils = new ImporterUtils();
$field_mapping = $import_utils->fieldMapping();
extract($field_mapping);
?>
<div class="student-mapping-box">
    <div class="custom-toast js-mandatory-error-box" style="display: none">
        <span class="js-message"></span>
        <button type="button" class="btn-tost-close">
            <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9.66659 1.27337L8.72659 0.333374L4.99992 4.06004L1.27325 0.333374L0.333252 1.27337L4.05992 5.00004L0.333252 8.72671L1.27325 9.66671L4.99992 5.94004L8.72659 9.66671L9.66659 8.72671L5.93992 5.00004L9.66659 1.27337Z" fill="#222222"/>
            </svg>
        </button>
    </div>

    <form action="" id="field-mapping-form">
        <div class="mapping-box">
            <div class="mapping-box-head">
                <div class="row">
                    <div class="col-sm-6">
                        <span>System Lables</span>
                    </div>
                    <div class="col-sm-6">
                    <span>Mapping with</span>
                </div>
                </div>
            </div>
            <div class="mapping-box-list">

                <?php
                    $excel_heading_arr = [];
                    if (!empty($import_students)) {
                        $excel_heading_arr = $import_students['list_heading'];
                    }
                    if (!empty($db_field_names)) {
                        foreach ($db_field_names as $db_field_name) {
                            $db_field_label = array_key_exists($db_field_name, $db_field_aliasing) ? $db_field_aliasing[$db_field_name] :  $db_field_name;
                            ?>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <?php if (in_array($db_field_name, $required_fields)) { ?>
                                            <span class="form-req">Mandatory </span>
                                        <?php } ?>
                                        <input type="text" class="form-control" placeholder="<?php echo $db_field_label; ?>" >
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <select name="<?php echo $db_field_name; ?>" id="<?php echo $db_field_name; ?>">
                                        <option value="">Please select</option>
                                        <?php
                                            if (!empty($excel_heading_arr)) {
                                                foreach ($excel_heading_arr as $ex_heading) {
                                                    $selected = '';
                                                    if (isset($mapped_data) && $mapped_data) {
                                                        foreach ($mapped_data as $mapped_db_field => $mapped_excel_heading) {
                                                            if ($mapped_db_field == $db_field_name && $mapped_excel_heading == $ex_heading) {
                                                                $selected = 'selected="selected"';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                      <option value="<?php echo $ex_heading; ?>" <?php echo $selected; ?>><?php echo $ex_heading; ?></option>
                                                    <?php
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn brn-blue">Save & Continue</button>
        </div>
        <div class="js-loader" style="display:none; text-align: Center">
            Please Wait. <img src="../assets/img/ajax-loader.gif">
        </div>
    </form>
</div>
