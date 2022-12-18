$(document).ready(function(){

    $(".account").click(function() {
        window.location.href = '/account';
    });

    $(".login-button").click(function() {
        window.location.href = '/';
    });

    $(".forgot-pass-button").click(function() {
        window.location.href = '/login';
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

    $("#create_user_button").click(function() {
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
        
        Toast.fire({
            icon: 'success',
            title: 'Používateľ bol úspešne vytvorený'
        })
    });

    $("#change_pass_button").click(function() {
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
        
        Toast.fire({
            icon: 'error',
            title: 'Heslo sa nepodarilo zmeniť'
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
            window.location.href = '/sapReports';
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
