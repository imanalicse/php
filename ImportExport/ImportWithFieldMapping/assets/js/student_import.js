var $modal;
const base_url = 'http://localhost/codehub/php/ImportExport/ImportWithFieldMapping';

jQuery(document).ready(function(){
    //loadPreDefinedFieldMapping();
    $modal = $("#staticBackdrop");
    $(".js-import-student-button").click(function(){
        var _self = $(this);
        var initial_popup = $(_self).data("initial-popup");
        //loadPreDefinedFieldMapping();
        $modal.modal({
            backdrop: 'static',
            keyboard: false
        });
        if (initial_popup) {
            console.log('here 1');
            importBind();
            loadPreDefinedFieldMapping();
        } else {
            setModalTitle("Import From CSV/Excel");
            $(".modal-body").html('<div class="js-loader" style="display:block; text-align: Center">\n' +
                'Please Wait. <img src="../img/ajax-loader.gif" alt=""></div>');

            $.ajax({
                url: '/admin/student-importer/getPopupInitBody',
                type: 'POST',
                data: {},
                beforeSend: function (request) {
                },
                success: function (resp) {
                    $(".modal-body").html(resp);
                    console.log('here 2');
                    importBind();
                    loadPreDefinedFieldMapping();
                }
            });
        }
    });
});

function setModalTitle(title) {
    $(".js-student-import-modal .modal-header .modal-title").text(title);
}

function importBind() {
    var student_import_form = $(".student-import-form");
    student_import_form.find(".file_name").change(function (event){
        student_import_form.submit();
        event.target.value = '';
    });
    student_import_form.submit(function(e) {
        $(".js-message").empty();
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: base_url + '/ajax/upload_file.php',
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function (request) {
                $('.js-loader').show();
                $(".import-file-box").hide();
            },
            success: function (resp) {
                console.log('resp', resp);
                var response = JSON.parse(resp);
                if (response.status == true) {
                    loadFieldMapping();
                } else {
                    $(".import-file-box").show();
                    $('.js-loader').hide();
                    $(".js-message").html('<span class="error">'+ response.msg +'</span>');
                }
            },
            complete: function () {
                $(".js-import-student-button").data("initial-popup", 0);
            }
        });
    });
}

function loadFieldMapping() {
    $.ajax({
        url: base_url + '/ajax/fieldMapping.php',
        type: 'POST',
        data: {},
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function (request) {

        },
        success: function (result) {
            setModalTitle("Import From CSV/Excel - Field Mapping");
            $(".modal-body").html(result);
            fieldMappingFormEvent();
        },
        complete: function () {
            $('.js-loader').hide();
        }
    });
}

function loadPreDefinedFieldMapping() {
    var student_import_form = $("#init-student-mapping");
    var isTrue = true;
    student_import_form.on('click', function(e) {
        console.log('init-student-mapping');
        $(".js-message").empty();
        e.preventDefault();

        setModalTitle("Field Mapping");
        $(".modal-body").html('<div class="js-loader" style="display:block; text-align: Center">\n' +
            'Please Wait. <img src="../img/ajax-loader.gif" alt=""></div>');

        $.ajax({
            url: base_url + '/ajax/fieldMapping',
            type: 'POST',
            data: {preDefinedForm: isTrue},
            cache: false,
            beforeSend: function (request) {

            },
            success: function (result) {
                //console.log(result);
                //setModalTitle("Import From CSV/Excel - Field Mapping");
                $(".modal-body").html(result);
                fieldMappingFormEvent();
            },
            complete: function () {
                $('.js-loader').hide();
            }
        });
    });
}

function fieldMappingFormEvent() {
    var $js_mandatory_error_box = $(".js-mandatory-error-box");
    var $field_mapping_form = $("#field-mapping-form");
    var $is_pre_mapping = $("#field-mapping-form").data('premapping');
    var url = base_url + '/ajax/saveFieldMapping';

    if($is_pre_mapping){
        url = base_url + '/ajax/savePreMapping';
        console.log('url - '+url);
    }
    $field_mapping_form.find(':submit').prop('disabled', false);
    $field_mapping_form.submit(function (e) {
        e.preventDefault();
        $field_mapping_form.find(':submit').prop('disabled', true);
        var formData = new FormData(this);
        loadPreDefinedFieldMapping();
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function (request) {
                $(".js-message").html('');
                $js_mandatory_error_box.hide();
                $('.js-loader').show();
            },
            success: function (resp) {
                var response = JSON.parse(resp);
                console.log(response);
                if ( response.status == true && $is_pre_mapping == false ) {
                    itemListing();
                } else if( $is_pre_mapping == true && response.resetPopup== true ){
                    console.log('close modal and page refresh');
                    console.log(response);
                    window.location.reload(true);
                }
                else {
                    $field_mapping_form.find(':submit').prop('disabled', false);
                    $('.js-loader').hide();
                    $js_mandatory_error_box.slideDown();
                    $(".js-message").html(response.msg);
                    $js_mandatory_error_box.find(".btn-tost-close").click(function () {
                        $js_mandatory_error_box.slideUp();
                    });
                    $modal.animate({ scrollTop: 0 }, 'slow');
                }
            }
        });
    });
}

function itemListing() {
    $.ajax({
        url: base_url + '/ajax/itemListing',
        type: 'GET',
        cache: false,
        beforeSend: function (request) {

        },
        success: function (result) {
            $(".modal-body").html(result);
            setModalTitle("Import From CSV/Excel - Imported " + $(".js-total-record").val() + " Students");
            saveImportBinding();
            paginationBind();
            var next = $('.pagination-arrows').find('.js-next');
            console.log(next.length);
            var pagination_container = $(".js-pagination-container");
            var total_record = parseInt(pagination_container.find(".js-total-record").val());
            var item_per_page = parseInt(pagination_container.find(".js-item-per-page").val());
            var page = parseInt(pagination_container.find(".js-page").val());
            console.log(total_record);
            console.log(page);
            if(total_record <= item_per_page){
                pagination_container.find(".js-page").val(1);
                var nextBtn = $(".pagination-arrows .btn");
                if(nextBtn.hasClass("js-next")){
                    nextBtn.removeClass("js-next");
                }
            }
        },
        complete: function () {
            $('.js-loader').hide();
        }
    });
}

function saveImportBinding() {
    var $import_save_button = $(".js-save-import-data");
    $(".js-message").empty();
    var $errorMessage = $("duplicate-student-message");
    if($errorMessage.length == 0){
        $import_save_button.prop('disabled', false);
    }
    //$import_save_button.prop('disabled', false);

    $import_save_button.click(function () {
        $(".js-message").empty();
        $import_save_button.prop('disabled', true);
        $.ajax({
            url: base_url + '/ajax/saveImportData',
            type: 'POST',
            cache: false,
            beforeSend: function (request) {
                $('.js-saving-loader').show();
            },
            // xhr: function () {
            //     var xhr = new window.XMLHttpRequest();
            //     xhr.addEventListener("progress", function (e) {
            //         console.log('e.currentTarget.response', e.currentTarget.response);
            //         $("#generateReport").html(e.currentTarget.response);
            //     });
            //     return xhr;
            // },
            success: function (result) {
                console.log(result);
                $import_save_button.prop('disabled', false);
                try {
                     var response = JSON.parse(result);
                    $(".js-message").html(response.msg);
                    $import_save_button.prop('disabled', true);
                } catch (e) {
                    console.log(e.message);
                }
            },
            complete: function () {
                $('.js-saving-loader').hide();
            }
        });
    });
}

function paginationBind() {
    var pagination_container = $(".js-pagination-container");
    pagination_container.find(".js-prev").click(function () {
        paginate($(this));
    });
    pagination_container.find(".js-next").click(function () {
        paginate($(this));
    });

    $(".js-item-per-page-dropdown li").click(function () {
        var item_per_page = parseInt($(this).find("a").text());
        console.log(item_per_page);
        var pagination_container = $(".js-pagination-container");
         pagination_container.find(".js-item-per-page").val(item_per_page);
         pagination_container.find(".js-page").val(0);
         $(".js-item-per-page-value").text(item_per_page);
         pagination_container.find(".js-next").trigger("click");
    });
}

function paginate(_self) {
    var pagination_container = $(".js-pagination-container");
    var total_record = parseInt(pagination_container.find(".js-total-record").val());
    var item_per_page = parseInt(pagination_container.find(".js-item-per-page").val());
    var page = parseInt(pagination_container.find(".js-page").val());

    // if(total_record > item_per_page){
    //     var items = (total_record / item_per_page);
    //     console.log(items);
    //     page = Math.ceil(items);
    //     console.log(items);
    // }

    console.log(total_record);
    console.log(item_per_page);
    console.log(page);
    if (_self.hasClass("js-prev")) {
        if (page == 1) return false;
        page = page > 1 ? page - 1 : page;
    }
    if (_self.hasClass("js-next")){
        if(page * item_per_page >= total_record) {
            console.log('yes');
            return false;
        }
        page = page + 1;
    }


    $.ajax({
        url: base_url + '/ajax/paging',
        type: 'POST',
        data: {
            total_record: total_record,
            item_per_page: item_per_page,
            page: page,
        },
        cache: false,
        beforeSend: function (request) {
            $('.js-loader').show();
        },
        success: function (result) {
            var htmlTbl = jQuery.parseHTML(result);
            //console.log(htmlTbl);
            var newTableData = '';
            var errorClass = '';
            $.each( htmlTbl, function( i, el ) {
                var rows = jQuery.parseHTML(el.innerHTML);
                var newTableRowData = '';
                $.each( rows, function( j, eli ) {
                    if(j == (rows.length -1) && jQuery.trim(eli.outerText) == 'true'){
                        rows.splice(j,1);
                        errorClass = 'list-error'
                    } else {
                        errorClass = ''
                        newTableRowData += '<td>'+eli.innerHTML+'</td>';
                    }
                });
                newTableData += '<tr class="'+errorClass+'">'+newTableRowData+'</tr>';
            });
            //console.log(newTableData);
            $(".js-item-list-table tbody").html(newTableData);
            pagination_container.find(".js-page").val(page);
            var lower_limit = (page - 1) * item_per_page + 1;
            var upper_limit = page * item_per_page > total_record ? total_record : page * item_per_page ;
            var pagination_counter = lower_limit + ' - ' + upper_limit + ' of ' + total_record;
            pagination_container.find(".js-pagination-counter").html(pagination_counter);
        },
        complete: function () {
            $('.js-loader').hide();
        }
    });
}