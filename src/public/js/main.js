$(document).ready(function(){

    // Initialize Toast -->
    const Toast = Swal.mixin({
        toast: true,
        position: 'bottom-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })
    // <-- Initialize Toast

    // Authorization forms -->

    // Submit login on enter
    $("#login-form").keypress(e => {
        if (e.which === 13) {
            $('#login-form').submit();
        }
    });

    // Create first user form -->
    $("#first-user-form").on("submit", function(e) {
        e.preventDefault();
        
        let email = $("#first-user-email").val();
        let csrf = $("#first-user-button").data("csrf");

        $.ajax({
            url: "/register",
            type: "POST",
            data: {
                "_token": csrf,
                "email": email
            }
        }).done(function(response) {
            window.location.href = '/login';
        }).fail(function(response) {
            $.fn.createFirstUserClearForm();

            if (typeof response.responseJSON != 'undefined') {
                if (response.status === 422) {
                    let errors = response.responseJSON.errors;

                    if (typeof errors.email != 'undefined') {
                        $("#first-user-email").css("border-color", "red");
    
                        errors.email.forEach(e => {
                            $("#first-user-email-errors").append("<p>" + e + "</p>");
                        });
                    }
                } else if (typeof response.responseJSON.displayMessage != 'undefined') {
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.displayMessage
                    })
                }
            }else{
                Toast.fire({
                    icon: 'error',
                    title: 'Niečo sa pokazilo. Prosím, skúste to neskôr.'
                })
            }
        })
    });

    $.fn.createFirstUserClearForm = function(isDone = false){ 
        if (isDone) {
            $("#first-user-email").val("");
        }

        $("#create-user-email").css("border-color", "var(--primary)");
        $("#create-user-email-errors").empty();
    }

    $("#first-user-form").keypress(e => {
        if (e.which === 13) {
            $('#first-user-form').submit();
        }
    });
    // <-- Create first user form
    
    // Create user form -->
    $(".create-user").click(function() {
        $("#create-user-modal").css("display", "flex");
    });

    $("#create-user-form").on("submit", function(e) {
        e.preventDefault();

        let email = $("#create-user-email").val();
        let csrf = $("#create-user-button").data("csrf");

        $.ajax({
            url: "/register",
            type: "POST",
            data: {
                "_token": csrf,
                "email": email
            }
        }).done(function(response) {
            let message = jQuery.parseJSON(response);

            Toast.fire({
                icon: 'success',
                title: message.displayMessage
            })

            $(".modal-box").css("display", "none");

            $.fn.createUserClearForm(true);
        }).fail(function(response) {
            $.fn.createUserClearForm();

            if (typeof response.responseJSON != 'undefined') {
                if (response.status === 422) {
                    let errors = response.responseJSON.errors;

                    if (typeof errors.email != 'undefined') {
                        $("#create-user-email").css("border-color", "red");

                        errors.email.forEach(e => {
                            $("#create-user-email-errors").append("<p>" + e + "</p>");
                        });
                    }
                } else if (typeof response.responseJSON.displayMessage != 'undefined') {
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.displayMessage
                    })
                }
            }else{       
                Toast.fire({
                    icon: 'error',
                    title: 'Niečo sa pokazilo. Prosím, skúste to neskôr.'
                })
            }
        })
    });

    $.fn.createUserClearForm = function(isDone = false){ 
        if (isDone) {
            $("#create-user-email").val("");
        }

        $("#create-user-email").css("border-color", "var(--primary)");
        $("#create-user-email-errors").empty();
    }

    $("#create-user-form").keypress(e => {
        if (e.which === 13) {
            $('#create-user-form').submit();
        }
    });
    // <-- Create user form

    // Change password form -->
    $(".change-pass").click(function() {
        $("#change-pass-modal").css("display", "flex");
    });

    $("#change-pass-form").on("submit", function(e) {
        e.preventDefault();

        let old = $("#change-pass-old").val();
        let new1 = $("#change-pass-new1").val();
        let new2 = $("#change-pass-new2").val();

        let csrf = $("#change-pass-button").data("csrf");

        $.ajax({
            url: "/change-password",
            type: "POST",
            data: {
                "_token": csrf,
                "old_password": old,
                "new_password": new1,
                "new_password_confirmation": new2
            }
        }).done(function(response) {
            let message = jQuery.parseJSON(response);

            Toast.fire({
                icon: 'success',
                title: message.displayMessage
            })

            $(".modal-box").css("display", "none");

            $.fn.changePassClearForm(true);
        }).fail(function(response) {
            $.fn.changePassClearForm();

            if (typeof response.responseJSON != 'undefined') {
                if (response.status === 422) {
                    let errors = response.responseJSON.errors;

                    if (typeof errors.old_password != 'undefined') {
                        $("#change-pass-old").css("border-color", "red");
    
                        errors.old_password.forEach(e => {
                            $("#change-pass-old-errors").append("<p>" + e + "</p>");
                        });
                    }
    
                    if (typeof errors.new_password != 'undefined') {
                        $("#change-pass-new1").css("border-color", "red");
    
                        errors.new_password.forEach(e => {
                            $("#change-pass-new1-errors").append("<p>" + e + "</p>");
                        });
                    }
                } else if (typeof response.responseJSON.displayMessage != 'undefined') {
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.displayMessage
                    })
                }
            }else{       
                Toast.fire({
                    icon: 'error',
                    title: 'Niečo sa pokazilo. Prosím, skúste to neskôr.'
                })
            }
        })
    });

    $.fn.changePassClearForm = function(isDone = false){ 
        if (isDone) {
            $("#change-pass-old").val("");
            $("#change-pass-new1").val("");
            $("#change-pass-new2").val("");
        }

        $("#change-pass-old").css("border-color", "var(--primary)");
        $("#change-pass-new1").css("border-color", "var(--primary)");
        $("#change-pass-new2").css("border-color", "var(--primary)");

        $("#change-pass-old-errors").empty();
        $("#change-pass-new1-errors").empty();
        $("#change-pass-new2-errors").empty();
    }

    $("#change-pass-form").keypress(e => {
        if (e.which === 13) {
            $('#change-pass-form').submit();
        }
    });
    // <-- Change password form

    // Forgotten password form -->
    $("#forgot-pass-form").on("submit", function(e) {
        e.preventDefault();

        let email = $("#forgot-pass-email").val();
        let csrf = $("#forgot-pass-button").data("csrf");

        $.ajax({
            url: "/forgot-password",
            type: "POST",
            data: {
                "_token": csrf,
                "email": email
            }
        }).done(function(response) {
            let message = jQuery.parseJSON(response);

            Toast.fire({
                icon: 'success',
                title: message.displayMessage
            })

            $.fn.forgotPassClearForm(true);
        }).fail(function(response) {
            $.fn.forgotPassClearForm();

            if (typeof response.responseJSON != 'undefined') {
                if (response.status === 422) {
                    if (typeof response.responseJSON != 'undefined') {
                        let errors = response.responseJSON.errors;
        
                        if (typeof errors.email != 'undefined') {
                            $("#forgot-pass-email").css("border-color", "red");
        
                            errors.email.forEach(e => {
                                $("#forgot-pass-email-errors").append("<p>" + e + "</p>");
                            });
                        }
                    }
                } else if (typeof response.responseJSON.displayMessage != 'undefined') {
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.displayMessage
                    })
                }
            }else{       
                Toast.fire({
                    icon: 'error',
                    title: 'Niečo sa pokazilo. Prosím, skúste to neskôr.'
                })
            }
        })
    });

    $.fn.forgotPassClearForm = function(isDone = false){ 
        if (isDone) {
            $("#forgot-pass-email").val("");
        }

        $("#forgot-pass-email").css("border-color", "var(--primary)");
        $("#forgot-pass-email-errors").empty();
    }

    $("#forgot-pass-form").keypress(e => {
        if (e.which === 13) {
            $('#forgot-pass-form').submit();
        }
    });
    // <-- Forgotten password form
    // <-- Authorization forms

    // Closing modals -->
    $(".close-modal").click(function() {
        $(".modal-box").css("display", "none");
    });

    $('.cancel').click(function(){
        $(".modal-box").css("display", "none");
    })
    // <-- Closing modals

    // Account modals -->
    $(".add_account_button i").click(function() {
        $("#create-account-modal").css("display", "flex");
    });

    $(".edit_account").click(function() {
        let account_id = $(this).data("id");
        let account_title = $(this).data("title");
        let account_sap_id = $(this).data("sap");

        $("#edit-account-modal").css("display", "flex");
        $("#edit-account-modal > .modal > #edit-account-form").data("id", account_id);
        $("#edit-account-name").val(account_title);
        $("#edit-account-sap-id").val(account_sap_id);
    });

    $(".delete_account").click(function() {
        let account_id = $(this).data("id");
        $("#delete-account-modal").css("display", "flex");
        $("#delete-account-modal > .modal > #delete-account-form").data("id", account_id);
    });
    // <-- Account modals

    // Financial accounts -->

    $(".account").click(function(){
        var account_id = $(this).data("id");
        window.location.href = '/accounts/'+account_id+'/operations';
    });

    // Financial accounts filter operations-->
    
    $("#filter-operations").click(function(){
        let account_id = $(this).data("account-id");
        let date_from = $('#filter-operations-from').val();
        let date_to = $('#filter-operations-to').val();
        let error = $(this).data("date-errors");
        let url ='/accounts/'+account_id+'/operations';

        if (date_from != "" || date_to != ""){
            url += '?';
        }
        if (date_from != ""){
            url += 'from=' + date_from
        }
        if (date_to != ""){
            if (date_from != ""){
                url += '&';
            }
            url += 'to=' + date_to
        }
        window.location.href = url;
    });

    // <-- Financial accounts filter operations
    $(".toggle-button").change(function(){
        let account_id = $(this).data("account-id");
        if($(this).attr('checked')){
            window.location.href = '/accounts/'+account_id+'/operations';
        }else{
            window.location.href = '/accounts/'+account_id+'/sap-reports';
        }
    })

    // Financial accounts forms -->
    
    $("#create-account-form").keypress(e => {
        if (e.which === 13) {
            $('#create-account-form').submit();
        }
    });
    // Create financial account form -->
    $("#create-account-form").on("submit", function(e) {
        e.preventDefault();

        $("#create-account-button").attr("disabled", true);

        let title = $("#add-account-name").val();
        let sapId = $("#add-account-sap-id").val();
        let csrf = $("#create-account-button").data("csrf");

        $.ajax({
            url: "/accounts",
            type: "POST",
            data: {
                "_token": csrf,
                'title': title,
                'sap_id': sapId
            }
        }).done(function(response) {
            let message = jQuery.parseJSON(response);

            Toast.fire({
                icon: 'success',
                title: message.displayMessage
            })
             location.reload()
            $(".modal-box").css("display", "none");

            $.fn.createAccountClearForm(true);
        }).fail(function(response) {
            $.fn.createAccountClearForm();
            if (typeof response.responseJSON != 'undefined'){
                if (response.status === 422) {
                    let errors = response.responseJSON.errors;

                    if (typeof errors.title != 'undefined') {
                        $("#add-account-name").css("border-color", "red");

                        errors.title.forEach(e => {
                            $("#add-account-name-errors").append("<p>" + e + "</p>");
                        });
                    }
                    if (typeof errors.sap_id != 'undefined') {
                        $("#add-account-sap-id").css("border-color", "red");
                        errors.sap_id.forEach(e => {
                            $("#add-account-sap-id-errors").append("<p>" + e + "</p>");
                        });
                    }
                    
                } else if (typeof response.responseJSON.displayMessage != 'undefined') {
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.displayMessage
                    })
                }
            }else{
                Toast.fire({
                    icon: 'error',
                    title: 'Niečo sa pokazilo. Prosím, skúste to neskôr.'
                })
            }
        })

    });


    $.fn.createAccountClearForm = function(isDone = false){ 

        if (isDone) {
            $("#add-account-name").val("");
            $("#add-account-sap-id").val("");
        }

        $("#create-account-button").attr("disabled", false);

        $("#add-account-name").css("border-color", "var(--primary)");
        $("#add-account-sap-id").css("border-color", "var(--primary)");

        $("#add-account-name").empty();
        $("#add-account-sap-id").empty();
        $("#add-account-sap-id-errors").empty();
        $("#add-account-name-errors").empty();


    }
    // <-- Create financial account form

    // Edit financial account form -->

    $("#edit-account-form").on("submit", function(e) {
        e.preventDefault();
        
        let account_id =  $(this).data("id");
    
        let title = $("#edit-account-name").val();
        let sapId = $("#edit-account-sap-id").val();

        let csrf = $("#edit-account-button").data("csrf");

        $.ajax({
            url: "/accounts/" + account_id,
            type: "PUT",
            data: {
                "_token": csrf,
                'title': title,
                'sap_id': sapId
            }
        }).done(function(response) {
            let message = jQuery.parseJSON(response);

            Toast.fire({
                icon: 'success',
                title: message.displayMessage
            })
            location.reload();
            $(".modal-box").css("display", "none");

            $.fn.editAccountClearForm(true);
        }).fail(function(response) {
            $.fn.editAccountClearForm();
            if (typeof response.responseJSON != 'undefined'){
                if (response.status === 422) {
                    let errors = response.responseJSON.errors;

                    if (typeof errors.title != 'undefined') {
                        $("#edit-account-name").css("border-color", "red");

                        errors.title.forEach(e => {
                            $("#edit-account-name-errors").append("<p>" + e + "</p>");
                        });
                    }
                    if (typeof errors.sap_id != 'undefined') {
                        $("#edit-account-sap-id").css("border-color", "red");
                        errors.sap_id.forEach(e => {
                            $("#edit-account-sap-id-errors").append("<p>" + e + "</p>");
                        });
                    }
                    
                } else if (typeof response.responseJSON.displayMessage != 'undefined') {
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.displayMessage
                    })
                }
            }else{    
                Toast.fire({
                    icon: 'error',
                    title: 'Niečo sa pokazilo. Prosím, skúste to neskôr.'
                })
            }
        })

    });


    $.fn.editAccountClearForm = function(isDone = false){ 

        if (isDone) {
            $("#edit-account-name").val("");
            $("#edit-account-sap-id").val("");
        }

        $("#edit-account-name").css("border-color", "var(--primary)");
        $("#edit-account-sap-id").css("border-color", "var(--primary)");

        $("#edit-account-name").empty();
        $("#edit-account-sap-id").empty();
        $("#edit-account-sap-id-errors").empty();
        $("#edit-account-name-errors").empty();
    }

    // <-- Edit financial account form

    // Delete financial account form -->

    $("#delete-account-form").on("submit", function(e) {
        e.preventDefault();

        let account_id =  $(this).data("id");

        let csrf = $("#create-account-button").data("csrf");

        $.ajax({
            url: "/accounts/" + account_id,
            type: "DELETE",
            data: {
                "_token": csrf
            }
        }).done(function(response) {
            let message = jQuery.parseJSON(response);

            Toast.fire({
                icon: 'success',
                title: message.displayMessage
            })
            location.reload();
            $(".modal-box").css("display", "none");

            $.fn.createAccountClearForm(true);
        }).fail(function(response) {
            $.fn.createAccountClearForm();
            if (typeof response.responseJSON != 'undefined'){
                if (response.status === 422) {
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.displayMessage
                    })
                }
            }else{    
                Toast.fire({
                    icon: 'error',
                    title: 'Niečo sa pokazilo. Prosím, skúste to neskôr.'
                })
            }
        })

    });

    // <-- Delete financial account form
    // <-- Financial accounts forms
    // <-- Financial accounts

    // --> SAP reports

    $("#reports-filter").click(function(){
        
        let account_id = $(this).data("account-id");
        let date_from = $('#filter-reports-from').val();
        let date_to = $('#filter-reports-to').val();
        let url ='/accounts/'+account_id+'/sap-reports';

        if (date_from != "" || date_to != ""){
            url += '?';
        }
        if (date_from != ""){
            url += 'from=' + date_from
        }
        if (date_to != ""){
            if (date_from != ""){
                url += '&';
            }
            url += 'to=' + date_to
        }
        window.location.href = url;

    });

    // --> SAP reports forms

    // --> add SAP report form
    $("#add-sap-report").click(function(){
        let account_id = $(this).data("account-id");
        $("#add-report-modal").css("display","flex");
        $("#add-report-modal > .modal > #create-report-form").data("account-id", account_id);
    })

    $("#create-report-form").on("submit", function(e){
        e.preventDefault();

        $("#create-report-button").attr("disabled", true);

        let account_id =  $(this).data("account-id");
        let csrf = $("#create-report-button").data("csrf");

        var fileUpload = $("#report-file").get(0);  
        var files = fileUpload.files;  
        var fileData = new FormData(); 
        fileData.append('sap_report', files[0] ?? '');  

        fileData.append('_token', csrf);
        
        $.ajax({
            url: "/accounts/" + account_id + '/sap-reports',
            type: "POST",
            contentType: false, // Not to set any content header  
            processData: false, // Not to process data  
            data: fileData
        }).done(function(response) {
            let message = jQuery.parseJSON(response);

            Toast.fire({
                icon: 'success',
                title: message.displayMessage
            })
            location.reload();

            $(".modal-box").css("display", "none");

            $.fn.createReportClearForm(true);
        }).fail(function(response) {
            $.fn.createReportClearForm();
            if (typeof response.responseJSON != 'undefined'){
                if (response.status === 422) {
                    let errors = response.responseJSON.errors;

                    if (typeof errors.sap_report != 'undefined') {
                        $("#operation-file").css("border-color", "red");

                        errors.sap_report.forEach(e => {
                            $("#add-sap-report-errors").append("<p>" + e + "</p>");
                        });
                    }
                    
                } else if (typeof response.responseJSON.displayMessage != 'undefined') {
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.displayMessage
                    })
                }
            }else{   
                Toast.fire({
                    icon: 'error',
                    title: 'Niečo sa pokazilo. Prosím, skúste to neskôr.'
                })
            }

        })
    });

    // <-- add SAP report form
    $.fn.createReportClearForm = function(isDone = false){ 

        if (isDone) {
            $("#operation-file").val("");
        }

        $("#create-report-button").attr("disabled", false);

        $("#operation-file").css("border-color", "var(--primary)");
        $("#add-sap-report-errors").css("border-color", "var(--primary)");

        $("#operation-file").empty();
        $("#add-sap-report-errors").empty();
    }

    // <-- SAP reports forms

    // --> delete SAP report form

    $(".report-delete").click(function(){
        let report_id = $(this).data("report-id");
        $("#delete-report-form").data("report-id", report_id);
        $("#delete-report-modal").css("display", "flex");
        $("#delete-report-modal").css("display", "flex");
    });

    $("#delete-report-form").on("submit", function(e) {
        e.preventDefault();

        let report_id =  $(this).data("report-id");

        let csrf = $("#delete-report-button").data("csrf");

        $.ajax({
            url: "/sap-reports/" + report_id,
            type: "DELETE",
            data: {
                "_token": csrf
            }
        }).done(function(response) {
            let message = jQuery.parseJSON(response);

            Toast.fire({
                icon: 'success',
                title: message.displayMessage
            })
            location.reload();
            $(".modal-box").css("display", "none");

        }).fail(function(response) {
            if (typeof response.responseJSON != 'undefined'){
                if (response.status === 422) {
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.displayMessage
                    })
                }
            }else{   
                Toast.fire({
                    icon: 'error',
                    title: 'Niečo sa pokazilo. Prosím, skúste to neskôr.'
                })
            }
        })

    });

    // <-- delete SAP report form

    // <-- SAP reports


    // --> Financial operations

    // --> Financial operations export

    $("#operations-export").click(function(){
        
        let account_id = $(this).data("account-id");
        let date_from = $('#filter-operations-from').val();
        let date_to = $('#filter-operations-to').val();
        let url ='/accounts/'+account_id+'/operations/export';

        if (date_from != "" || date_to != ""){
            url += '?';
        }
        if (date_from != ""){
            url += 'from=' + date_from
        }
        if (date_to != ""){
            if (date_from != ""){
                url += '&';
            }
            url += 'to=' + date_to
        }
        window.location.href = url;

    });

    // <-- Financial operations export

    // <-- Financial operations detail

    $(".operation-detail").click(function(){
        $("#operation-modal").css("display", "flex");

        let operation_id = $(this).data("operation-id");
        let csrf = $(this).data("csrf");

        $.ajax({
            url: "/operations/" + operation_id,
            type: "GET",
            data: {
                "_token": csrf
            }
        }).done(function(response) {

            if (response.operation.operation_type.expense == 0) {
                $("#operation_main_type").html("Príjem");
            } else {
                $("#operation_main_type").html("Výdavok");
            }

            $("#operation_type").html(response.operation.operation_type.name);
            $("#operation_name").html(response.operation.title);
            $("#operation_subject").html(response.operation.subject);
            $("#operation_sum").html(response.operation.sum + " €");
            $("#operation_date").html(response.operation.date);

            if (response.operation.operation_type.lending == 1) {
                $("#operation_date_until").html(operation.lending.expected_date_of_return);
            } else {
                $("#operation_date_until").hide();
            }
            $("#operation-attachment-button").attr("onclick", 'location.href="/operations/'+ operation_id +'/attachment"')
            if (response.operation.attachment == null){
                $("#operation-attachment-button").hide();
            }

        }).fail(function(response) {
            if (typeof response.responseJSON != 'undefined'){
                if (response.status === 422) {
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.displayMessage
                    })
                }
            }else{   
                Toast.fire({
                    icon: 'error',
                    title: 'Niečo sa pokazilo. Prosím, skúste to neskôr.'
                })
            }
        })
    })

    // --> Financial operations detail

    // --> Financial operations forms

    // --> Delete operation form

    $(".operation-delete").click(function(){
        let operation_id = $(this).data("operation-id");
        $("#delete-operation-form").data("operation-id", operation_id);
        $("#delete-operation-modal").css("display", "flex");
    })

    $("#delete-operation-form").on("submit", function(e) {
        e.preventDefault();

        let operation_id =  $(this).data("operation-id");

        let csrf = $("#delete-operation-button").data("csrf");

        $.ajax({
            url: "/operations/" + operation_id,
            type: "DELETE",
            data: {
                "_token": csrf
            }
        }).done(function(response) {
            let message = jQuery.parseJSON(response);

            Toast.fire({
                icon: 'success',
                title: message.displayMessage
            })
            location.reload();

            $(".modal-box").css("display", "none");

        }).fail(function(response) {
            if (typeof response.responseJSON != 'undefined'){
                if (response.status === 422) {
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.displayMessage
                    })
                }
            }else{   
                Toast.fire({
                    icon: 'error',
                    title: 'Niečo sa pokazilo. Prosím, skúste to neskôr.'
                })
            }
        })

    });

    // <-- Delete operation form

    // --> Check/Uncheck operation

    $(".operation-check").click(function(){
        let operation_id = $(this).data("operation-id");
        $("#check-operation-form").data("operation-id", operation_id);
        let operation_checked = $(this).data("operation-checked");
        $("#check-operation-form").data("operation-checked", operation_checked);
        $("#check-operation-modal").css("display", "flex");
    })


    $("#check-operation-form").on("submit", function(e) {
        e.preventDefault();

        let operation_id =  $(this).data("operation-id");
        let operation_checked = ($(this).data("operation-checked") - 1) *(-1);

        let csrf = $("#check-operation-button").data("csrf");

        $.ajax({
            url: "/operations/" + operation_id,
            type: "PATCH",
            data: {
                '_token': csrf,
                'checked': operation_checked    
            }
        }).done(function(response) {
            let message = jQuery.parseJSON(response);

            Toast.fire({
                icon: 'success',
                title: message.displayMessage
            })
            location.reload();
            $(".modal-box").css("display", "none");

        }).fail(function(response) {

            Toast.fire({
                icon: 'error',
                title: 'Niečo sa pokazilo. Prosím, skúste to neskôr.'
            })

        })
        
    });
    // <-- Check/Uncheck operation

    // --> Create operaton form

    $("#create_operation").click(function(){
        let account_id = $(this).data("account-id");
        $("#create-operation-form").data("account-id", account_id);
        $("#create-operation-modal").css("display", "flex");    
    })

    $("#create-operation-form").on("submit", function(e) {
        e.preventDefault();

        $("#create-operation-button").attr("disabled", true);

        let csrf = $("#create-operation-button").data("csrf");
        let account_id = $(this).data("account-id");
        let expense_income = $("input[name='operation_type']:checked").val();
        let operation_type_id = $("#operation_choice").val();
        let title = $("#add-operation-name").val();
        let subject = $("#add-operation-subject").val();
        let sum = $("#add-operation-sum").val();
        let date = $("#add-operation-to").val();

        var fileUpload = $("#operation-file").get(0);  
        var files = fileUpload.files;  

        var fileData = new FormData(); 

        fileData.append('_token', csrf);
        fileData.append('title', title);
        fileData.append('date', date);
        fileData.append('operation_type_id', operation_type_id);
        fileData.append('subject', subject);
        fileData.append('sum', sum);
        fileData.append('attachment', files[0] ?? '');  

        $.ajax({
            url: "/accounts/" + account_id + "/operations",
            type: "POST",
            contentType: false,
            processData: false,
            data: fileData

        }).done(function(response) {
            let message = jQuery.parseJSON(response);

            Toast.fire({
                icon: 'success',
                title: message.displayMessage
            })
            location.reload();

            $(".modal-box").css("display", "none");

            $.fn.createOperationClearForm(true);
        }).fail(function(response) {
            $.fn.createOperationClearForm();
            if (typeof response.responseJSON != 'undefined'){
                if (response.status === 422) {
                    let errors = response.responseJSON.errors;
                    if (files.length == 0){
                        $("#operation-file").css("border-color", "red");
                        $("#add-operation-attachment-errors").append("<p>Pole s prílohou je potrebné vyplniť</p>");
                    }
                    if (typeof errors.attachment != 'undefined') {
                        $("#operation-file").css("border-color", "red");

                        errors.attachment.forEach(e => {
                            $("#add-operation-attachment-errors").append("<p>" + e + "</p>");
                        });
                    }
                    if (typeof errors.date != 'undefined') {
                        $("#add-operation-to").css("border-color", "red");
                        errors.date.forEach(e => {
                            $("#add-operation-date-errors").append("<p>" + e + "</p>");
                        });
                    }
                    if (typeof errors.operation_type_id != 'undefined') {
                        $("#add-operation-type").css("border-color", "red");
                        $("#add-operation-type-errors").append("<p>Neplatný typ operácie</p>");
                    }
                    if (typeof errors.subject != 'undefined') {
                        $("#add-operation-subject").css("border-color", "red");

                        errors.subject.forEach(e => {
                            $("#add-operation-subject-errors").append("<p>" + e + "</p>");
                        });
                    }            
                    if (typeof errors.sum != 'undefined') {
                        $("#add-operation-sum").css("border-color", "red");

                        errors.sum.forEach(e => {
                            $("#add-operation-sum-errors").append("<p>" + e + "</p>");
                        });
                    }                
                    if (typeof errors.title != 'undefined') {
                        $("#add-operation-name").css("border-color", "red");

                        errors.title.forEach(e => {
                            $("#add-operation-title-errors").append("<p>" + e + "</p>");
                        });
                    }
                    
                } else if (typeof response.responseJSON.displayMessage != 'undefined') {
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.displayMessage
                    })
                }
            }else{   
                Toast.fire({
                    icon: 'error',
                    title: 'Niečo sa pokazilo. Prosím, skúste to neskôr.'
                })
            }
        })

    });

    $.fn.createOperationClearForm = function(isDone = false){ 

        if (isDone) {
            $("#operation-file").val("");
            $("#add-operation-to").val("");
            $("#add-operation-type").val("");
            $("#add-operation-subject").val("");
            $("#add-operation-sum").val("");
            $("#add-operation-name").val("");
        }

        $("#create-operation-button").attr("disabled", false);

        $("#operation-file").css("border-color", "var(--primary)");
        $("#add-operation-to").css("border-color", "var(--primary)");
        $("#add-operation-type").css("border-color", "var(--primary)");
        $("#add-operation-subject").css("border-color", "var(--primary)");
        $("#add-operation-sum").css("border-color", "var(--primary)");
        $("#add-operation-name").css("border-color", "var(--primary)");
        $("#add-operation-attachment-errors").css("border-color", "var(--primary)");
        $("#add-operation-date-errors").css("border-color", "var(--primary)");
        $("#add-operation-type-errors").css("border-color", "var(--primary)");
        $("#add-operation-subject-errors").css("border-color", "var(--primary)");
        $("#add-operation-sum-errors").css("border-color", "var(--primary)");
        $("#add-operation-title-errors").css("border-color", "var(--primary)");

        $("#operation-file").empty();
        $("#add-operation-to").empty();
        $("#add-operation-type").empty();
        $("#add-operation-subject").empty();
        $("#add-operation-sum").empty();
        $("#add-operation-name").empty();
        $("#add-operation-attachment-errors").empty();
        $("#add-operation-date-errors").empty();
        $("#add-operation-type-errors").empty();
        $("#add-operation-subject-errors").empty();
        $("#add-operation-sum-errors").empty();
        $("#add-operation-title-errors").empty();
    }

    // <-- Create operation form


    // --> Edit operaton form

    $(".operation-edit").click(function(){
        let account_id = $(this).data("account-id");
        let operation_id = $(this).data("operation-id");
        $("#edit-operation-form").data("account-id", account_id);
        $("#edit-operation-form").data("operation-id", operation_id);
        $("#edit-operation-modal").css("display", "flex");    

    })

    $("#edit-operation-form").on("submit", function(e) {
        e.preventDefault();

        $("#edit-operation-button").attr("disabled", true);

        let csrf = $("#edit-operation-button").data("csrf");
        let operation_id = $("#edit-operation-form").data("operation-id");
        let expense_income = $("input[name='edit_operation_type']:checked").val();
        let operation_type_id = $("#operation_edit_choice").val();
        let title = $("#edit-operation-name").val();
        let subject = $("#edit-operation-subject").val();
        let sum = $("#edit-operation-sum").val();
        let date = $("#edit-operation-to").val();

        var fileUpload = $("#edit-operation-file").get(0);  
        var files = fileUpload.files;  
        var fileData = new FormData(); 

        fileData.append('title', title);
        fileData.append('date', date);
        fileData.append('operation_type_id', operation_type_id);
        fileData.append('subject', subject);
        fileData.append('sum', sum);
        fileData.append('attachment', files[0] ?? '');  
        fileData.append('_token', csrf);
        fileData.append('_method', 'PUT');

        $.ajax({
            url: "/operations/" + operation_id,
            type: "POST",
            contentType: false,
            processData: false,
            data: fileData
        }).done(function(response) {
            let message = jQuery.parseJSON(response);

            Toast.fire({
                icon: 'success',
                title: message.displayMessage
            })
            location.reload();
            $(".modal-box").css("display", "none");

            $.fn.editOperationClearForm(true);
        }).fail(function(response) {
            $.fn.editOperationClearForm();
            if (typeof response.responseJSON != 'undefined'){
                if (response.status === 422) {
                    let errors = response.responseJSON.errors;
                    if (files.length == 0){
                        $("#edit-operation-file").css("border-color", "red");
                        $("#edit-operation-attachment-errors").append("<p>Pole s prílohou je potrebné vyplniť</p>");
                    }
                    if (typeof errors.attachment != 'undefined') {
                        $("#edit-operation-file").css("border-color", "red");

                        errors.attachment.forEach(e => {
                            $("#edit-operation-attachment-errors").append("<p>" + e + "</p>");
                        });
                    }
                    if (typeof errors.date != 'undefined') {
                        $("#edit-operation-to").css("border-color", "red");
                        errors.date.forEach(e => {
                            $("#edit-operation-date-errors").append("<p>" + e + "</p>");
                        });
                    }
                    if (typeof errors.operation_type_id != 'undefined') {
                        $("#edit-operation-type").css("border-color", "red");
                        $("#edit-operation-type-errors").append("<p>Neplatný typ operácie.</p>");;
                    }
                    if (typeof errors.subject != 'undefined') {
                        $("#edit-operation-subject").css("border-color", "red");

                        errors.subject.forEach(e => {
                            $("#edit-operation-subject-errors").append("<p>" + e + "</p>");
                        });
                    }            
                    if (typeof errors.sum != 'undefined') {
                        $("#edit-operation-sum").css("border-color", "red");

                        errors.sum.forEach(e => {
                            $("#edit-operation-sum-errors").append("<p>" + e + "</p>");
                        });
                    }                
                    if (typeof errors.title != 'undefined') {
                        $("#edit-operation-name").css("border-color", "red");

                        errors.title.forEach(e => {
                            $("#edit-operation-title-errors").append("<p>" + e + "</p>");
                        });
                    }
                    
                } else if (typeof response.responseJSON.displayMessage != 'undefined') {
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.displayMessage
                    })
                }
            }else{
                Toast.fire({
                    icon: 'error',
                    title: 'Niečo sa pokazilo. Prosím, skúste to neskôr.'
                })
            }
        })

    });

    $.fn.editOperationClearForm = function(isDone = false){ 

        if (isDone) {
            $("#operation-file").val("");
            $("#edit-operation-to").val("");
            $("#edit-operation-type").val("");
            $("#edit-operation-subject").val("");
            $("#edit-operation-sum").val("");
            $("#edit-operation-name").val("");
        }

        $("#edit-operation-button").attr("disabled", false);

        $("#operation-file").css("border-color", "var(--primary)");
        $("#edit-operation-to").css("border-color", "var(--primary)");
        $("#edit-operation-type").css("border-color", "var(--primary)");
        $("#edit-operation-subject").css("border-color", "var(--primary)");
        $("#edit-operation-sum").css("border-color", "var(--primary)");
        $("#edit-operation-name").css("border-color", "var(--primary)");
        $("#edit-operation-attachment-errors").css("border-color", "var(--primary)");
        $("#edit-operation-date-errors").css("border-color", "var(--primary)");
        $("#edit-operation-type-errors").css("border-color", "var(--primary)");
        $("#edit-operation-subject-errors").css("border-color", "var(--primary)");
        $("#edit-operation-sum-errors").css("border-color", "var(--primary)");
        $("#edit-operation-title-errors").css("border-color", "var(--primary)");

        $("#operation-file").empty();
        $("#edit-operation-to").empty();
        $("#edit-operation-type").empty();
        $("#edit-operation-subject").empty();
        $("#edit-operation-sum").empty();
        $("#edit-operation-name").empty();
        $("#edit-operation-attachment-errors").empty();
        $("#edit-operation-date-errors").empty();
        $("#edit-operation-type-errors").empty();
        $("#edit-operation-subject-errors").empty();
        $("#edit-operation-sum-errors").empty();
        $("#edit-operation-title-errors").empty();
    }

    // <-- Edit operaton form
    function updateSelectOptions(operation_type){
        switch(operation_type){
            case 'income':
                $(".expense_opt").css("display","none")
                $(".income_opt").css("display","flex")
                $("#operation_choice").val("default_opt")
                $("#edit_operation_choice").val("default_opt")
                $(".lending_opt").css("display","none")
                $(".edit_lending_opt").css("display","none")
                break;
            case 'expense':
                $(".income_opt").css("display","none")
                $(".expense_opt").css("display","flex")
                $("#operation_choice").val("default_opt")
                $("#edit_operation_choice").val("default_opt")
                $(".lending_opt").css("display","none")
                $(".edit_lending_opt").css("display","none")
                break;
        } 
    }

    $(".operation_type").change(function(){
        updateSelectOptions($(this).val())
    });

    // 5 -> lending to
    // 8 -> lending from
    // 9 -> return of lending
    function updateOperationForm(operation_category){
        if(operation_category == "9"){
            $(".add-operation-name").css("display","none");
            $(".add-operation-subject").css("display","none");
            $(".add-operation-sum").css("display","none");
            $(".add-operation-to").css("display","flex");
            $(".add-operation-expected-date").css("display","none");
            $(".operation-file").css("display","none");
            $(".choose-lending").css("display","flex");
            return;
        }
        if(operation_category == "5" ||
            operation_category == "8"){
            $(".add-operation-name").css("display","flex");
            $(".add-operation-subject").css("display","flex");
            $(".add-operation-sum").css("display","flex");
            $(".add-operation-to").css("display","flex");
            $(".add-operation-expected-date").css("display","flex");
            $(".operation-file").css("display","none");
            $(".choose-lending").css("display","none");
            return;
        }
        $(".add-operation-name").css("display","flex");
        $(".add-operation-subject").css("display","flex");
        $(".add-operation-sum").css("display","flex");
        $(".add-operation-to").css("display","flex");
        $(".add-operation-expected-date").css("display","none");
        $(".operation-file").css("display","flex");
        $(".choose-lending").css("display","none");

    }


    // 5 -> lending to
    // 8 -> lending from
    // 9 -> return of lending
    $("#operation_choice").change(function(){
        updateOperationForm($(this).val());
    })

    $("#edit_operation_choice").change(function(){
        updateOperationForm($(this).val());
    })

    // <-- Financial operations forms

    // <-- Financial operations
})
