jQuery(document).ready(function ($){
    $survey_form = $("#student_survey_form");

    $survey_form.find("input[type='checkbox']").change(function () {
        var _self = $(this);
        var input_wrapper = _self.parent();
        var is_checked = _self.is(":checked");
        console.log('is_checked=' + is_checked);
        //is_checked ? input_wrapper.find(".js-conditional-display").show() : input_wrapper.find(".js-conditional-display").hide();
        if (is_checked) {
            input_wrapper.find(".js-conditional-display").first().show()
        } else {
            input_wrapper.find(".js-conditional-display").first().hide();
        }
    });
    $survey_form.submit(function (e) {
        e.preventDefault();
        var testForm=jQuery("#student_survey_form").validate({
            errorPlacement: function(error, element) {
                if (element.attr('type') == 'radio') {
                    if (element.parents(".radio-button").length > 0) {
                        error.insertAfter(element.parents(".radio-button"));
                    } else {
                        error.insertAfter(element);
                    }
                } else {
                    error.insertAfter(element);
                }
            }
        });

        if(testForm.form()){
            var formData = $('#student_survey_form').serialize();
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