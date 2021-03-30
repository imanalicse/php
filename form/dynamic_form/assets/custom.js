function resetField($selector) {
    $selector.find("textarea").val("");
    $selector.find("input[type='text']").val("");
    $selector.find("input[type='radio']").prop('checked', false);
}
jQuery(document).ready(function ($){

    $survey_form = $("#student_survey_form");
    $survey_form.find("input[type='checkbox']").change(function () {
        var _self = $(this);
        var input_wrapper = _self.closest('.js-input-wrapper');
        var is_checked = _self.is(":checked");
        if (is_checked) {
            input_wrapper.find(".js-conditional-display").first().show()
        } else {
            resetField(input_wrapper.find(".js-conditional-display"));
            input_wrapper.find(".js-conditional-display").hide();
        }
    });

    $survey_form.find("input[type='radio']").change(function () {
        var _self = $(this);
        var value = _self.val();
        var input_wrapper = _self.closest('.js-input-wrapper');
        var conditional_value = input_wrapper.data("conditional-value");
        var is_checked = _self.is(":checked");
        if (is_checked && value == conditional_value) {
            input_wrapper.find(".js-conditional-display").first().show()
        } else {
            resetField(input_wrapper.find(".js-conditional-display"));
            input_wrapper.find(".js-conditional-display").hide();
        }
    });

    $.validator.addMethod("checkbox_validation", function(value, element, params) {
        var checked_length = $(element).parents('.checkbox-group').find('input[type="checkbox"]:checked').length;
        return checked_length > 0
    }, "Please select at least one!");

    $.validator.addClassRules({
        checkbox_validation: {
            checkbox_validation : true
        }
    });

    $survey_form.submit(function (e) {
        e.preventDefault();
        var testForm=jQuery("#student_survey_form").validate({
            // rules: {
            //     'reynolds_post_graduation_plan[]': {
            //         required: true,
            //         minLength: 1
            //     },
            // },
            errorPlacement: function(error, element) {
                if (element.attr('type') == 'radio') {
                    if (element.parents(".radio-button").length > 0) {
                        error.insertAfter(element.parents(".radio-button"));
                    } else {
                        error.insertAfter(element);
                    }
                }
                if (element.attr('type') == 'checkbox') {
                    if (element.parents(".checkbox-group").length > 0) {
                        console.log(error);
                        error.insertAfter(element.parents(".checkbox-group"));
                    } else {
                        error.insertAfter(element);
                    }
                } else {
                    error.insertAfter(element);
                }
            }
        });

        if(testForm.form()){
            var formData = $('#student_survey_form').serializeArray();
            // var myForm = document.getElementById('student_survey_form');
            // var formData = new FormData(myForm);
            console.log('formData', formData);
            $.ajax({
                url: 'save.php',
                data: formData,
                type: 'post',
                dataType: "json",
                beforeSend: function(request){
                    $('#LoadingAjax').show();
                },
                cache: false,
                success: function(data){

                },
                complete: function (request, json) {
                    $('#LoadingAjax').hide();
                },
            });
        }
    });
});