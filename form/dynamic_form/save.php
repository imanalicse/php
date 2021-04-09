<?php
include '../../functions.php';

$post_data = $_POST;
if (isset($post_data['data']['StudentSurvey']['extra'])) {
    $extra_fields = $post_data['data']['StudentSurvey']['extra'];
    waLog($extra_fields);
    foreach ($extra_fields as $field_name => $field_value) {
        if(is_array($field_value)) {
            if (array_key_exists('input_type_radio_selected_index', $field_value)) {

            }
        }
    }

    if (!empty($extra_fields) && is_array($extra_fields)) {
        foreach ($extra_fields as $field_name => $field_value) {
            if(is_array($field_value)) {
                $new_field_value = '';
                foreach ($field_value as $item) {
                    if(is_array($item)) {
                        if (array_key_exists('field_value', $item)) {
                            $new_field_value .= $item['field_value'];
                            if (array_key_exists('child_value', $item)) {
                                if (is_array($item['child_value']) && !empty($item['child_value'])) {
                                    foreach ($item['child_value'] as $item2) {
                                        if (!empty($item2['field_value'])) {
                                            $new_field_value2 = '';
                                            if (array_key_exists('child_value', $item2) && is_string($item2['child_value'])) {
                                                $new_field_value2 .= '(' . $item2['child_value'] . ')';
                                            }
                                            $new_field_value .= '(' . $item2['field_value'] . $new_field_value2 . ')';
                                        }
                                    }
                                } else if (!empty($item['child_value']) && is_string($item['child_value'])) {
                                    $new_field_value .= '(' . $item['child_value'] . '), ';
                                }
                            }
                        }
                    } else {
                        $new_field_value .= $item. ', ';
                    }
                }
                $extra_fields[$field_name] = $new_field_value;
            }
        }
    }

//    waLog('$extra_fields_new');
//    waLog($extra_fields);

    $student_data['data']['StudentSurvey']['extra_fields'] = $this->json_encode($extra_fields);
}