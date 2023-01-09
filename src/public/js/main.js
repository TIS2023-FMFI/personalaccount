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
            }

            Toast.fire({
                icon: 'error',
                title: 'Niečo sa pokazilo. Prosím, skúste to neskôr.'
            })
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
            }
                
            Toast.fire({
                icon: 'error',
                title: 'Niečo sa pokazilo. Prosím, skúste to neskôr.'
            })
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
            }
                
            Toast.fire({
                icon: 'error',
                title: 'Niečo sa pokazilo. Prosím, skúste to neskôr.'
            })
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
            }
                
            Toast.fire({
                icon: 'error',
                title: 'Niečo sa pokazilo. Prosím, skúste to neskôr.'
            })
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
        $("#edit-account-modal").css("display", "flex");
    });

    $(".delete_account").click(function() {
        $("#delete-account-modal").css("display", "flex");
    });
    // <-- Account modals

    $(".account").click(function() {
        window.location.href = '/accounts';
    });

    $(".operation-detail").click(function(){
        $("#operation-modal").css("display", "flex");
    })

    $("#create_operation").click(function(){
        $("#create-operation-modal").css("display", "flex");
    })

    function updateSelectOptions(operation_type){
        switch(operation_type){
            case 'income':
                $(".expense_opt").css("display","none")
                $(".income_opt").css("display","flex")
                $("#operation_choice").val("default_opt")
                $(".lending_opt").css("display","none")
                $(".edit_lending_opt").css("display","none")
                break;
            case 'expense':
                $(".income_opt").css("display","none")
                $(".expense_opt").css("display","flex")
                $("#operation_choice").val("default_opt")
                $(".lending_opt").css("display","none")
                $(".edit_lending_opt").css("display","none")
                break;
        } 
    }

    $(".operation_type").change(function(){
        updateSelectOptions($(this).val())
    });

    $("#operation_choice").change(function(){
        if($(this).val() == "lending_to" || 
        $(this).val() == "lending_from" ||
        $(this).val() == "return_of_lending"){
            $(".lending_opt").css("display","flex")
            return
        }
        $(".lending_opt").css("display","none")
    })

    $("#edit_operation_choice").change(function(){
        if($(this).val() == "lending_to" || 
        $(this).val() == "lending_from" ||
        $(this).val() == "return_of_lending"){
            $(".edit_lending_opt").css("display","flex")
            return
        }
        $(".edit_lending_opt").css("display","none")
    })

    $(".operation-check").click(function(){
        $("#check-operation-modal").css("display", "flex");
    })

    $(".operation-delete").click(function(){
        $("#delete-operation-modal").css("display", "flex");
    })

    $(".toggle-button").change(function(){
        if($(this).attr('checked')){
            window.location.href = '/accounts';
        }else{
            window.location.href = '/sap-reports';
        }
    })

    $("#add_sap_report").click(function(){
        $("#add-report-modal").css("display","flex")
    })

    $(".operation-edit").click(function(){
        $("#edit-operation-modal").css("display", "flex");
    })



})
