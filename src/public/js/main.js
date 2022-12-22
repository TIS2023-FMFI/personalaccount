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
    // Create user form -->
    $(".create-user").click(function() {
        $("#create-user-modal").css("display", "flex");
    });

    $("#create-user-button").click(function() {
        let email = $("#create-user-email").val();
        let csrf = $(this).data("csrf");

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

            let messages = jQuery.parseJSON(response.responseText);
            if(typeof messages.displayMessage != 'undefined') {
                Toast.fire({
                    icon: 'error',
                    title: messages.displayMessage
                })
            }
            
            if (typeof response.responseJSON != 'undefined') {
                let errors = response.responseJSON.errors;

                if (typeof errors.email != 'undefined') {
                    $("#create-user-email").css("border-color", "red");

                    errors.email.forEach(e => {
                        $("#create-user-email-errors").append("<p>" + e + "</p>");
                    });
                }
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
    // <-- Create user form

    // Change password form -->
    $(".change-pass").click(function() {
        $("#change-pass-modal").css("display", "flex");
    });

    $("#change-pass-button").click(function() {
        let old = $("#change-pass-old").val();
        let new1 = $("#change-pass-new1").val();
        let new2 = $("#change-pass-new2").val();

        let csrf = $(this).data("csrf");

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

            let messages = jQuery.parseJSON(response.responseText);
            if(typeof messages.displayMessage != 'undefined') {
                Toast.fire({
                    icon: 'error',
                    title: messages.displayMessage
                })
            }
            
            if (typeof response.responseJSON != 'undefined') {
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
    // <-- Change password form

    // Forgotten password form -->
    $(".forgot-pass-button").click(function() {
        let email = $("#forgot-pass-email").val();
        let csrf = $(this).data("csrf");

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

            let messages = jQuery.parseJSON(response.responseText);
            if(typeof messages.displayMessage != 'undefined') {
                Toast.fire({
                    icon: 'error',
                    title: messages.displayMessage
                })
            }

            if (typeof response.responseJSON != 'undefined') {
                let errors = response.responseJSON.errors;

                if (typeof errors.email != 'undefined') {
                    $("#forgot-pass-email").css("border-color", "red");

                    errors.email.forEach(e => {
                        $("#forgot-pass-email-errors").append("<p>" + e + "</p>");
                    });
                }
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

    $(".account").click(function() {
        window.location.href = '/account';
    });

    $(".bi-info-circle").click(function(){
        $("#operation-modal").css("display", "flex");
    })

    $("#create_operation").click(function(){
        $("#create-operation-modal").css("display", "flex");
    })

    $(".operation_type").change(function(){
        switch($(this).val()){
            case 'income':
                $(".expense_opt").css("display","none")
                $(".income_opt").css("display","flex")
                $("#operation_choice").val("default_opt")
                break;
            case 'expense':
                $(".income_opt").css("display","none")
                $(".expense_opt").css("display","flex")
                $("#operation_choice").val("default_opt")
                break;
        } 
    });

    $(".bi-check2-all").click(function(){
        $("#check-operation-modal").css("display", "flex");
    })

    $(".bi-trash3").click(function(){
        $("#delete-operation-modal").css("display", "flex");
    })

    $(".toggle-button").change(function(){
        if($(this).attr('checked')){
            window.location.href = '/account';
        }else{
            window.location.href = '/sap-reports';
        }
    })

    $("#add_sap_report").click(function(){
        $("#add-report-modal").css("display","flex")
    })

    $(".bi-pencil").click(function(){
        $("#edit-operation-modal").css("display", "flex");
    })

})
