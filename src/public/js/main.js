$(document).ready(function(){

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

    $(".account").click(function() {
        window.location.href = '/account';
    });

    $(".close-modal").click(function() {
        $(".modal-box").css("display", "none");
    });

    $(".change-pass").click(function() {
        $("#change-pass-modal").css("display", "flex");
    });

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

            $("#create-user-email").val("");
        }).fail(function(response) {
            let messages = jQuery.parseJSON(response.responseText);
            if(typeof messages.displayMessage != 'undefined') {
                Toast.fire({
                    icon: 'error',
                    title: messages.displayMessage
                })
            }
            //console.log(response.responseJSON.errors);
        })
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

            $("#change-pass-old").val("");
            $("#change-pass-new1").val("");
            $("#change-pass-new2").val("");
        }).fail(function(response) {
            let messages = jQuery.parseJSON(response.responseText);
            if(typeof messages.displayMessage != 'undefined') {
                Toast.fire({
                    icon: 'error',
                    title: messages.displayMessage
                })
            }
            //console.log(response.responseJSON.errors);
        })
    });

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

            $("#forgot-pass-email").val("");
        }).fail(function(response) {
            let messages = jQuery.parseJSON(response.responseText);
            if(typeof messages.displayMessage != 'undefined') {
                Toast.fire({
                    icon: 'error',
                    title: messages.displayMessage
                })
            }
            //console.log(response.responseJSON.errors);
        })
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

    $('.cancel').click(function(){
        $(".modal-box").css("display", "none");
    })

    $("#add_sap_report").click(function(){
        $("#add-report-modal").css("display","flex")
    })

    $(".bi-pencil").click(function(){
        $("#edit-operation-modal").css("display", "flex");
    })

})
