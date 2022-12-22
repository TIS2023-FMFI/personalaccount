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

    $(".operation-edit").click(function(){
        $("#edit-operation-modal").css("display", "flex");
    })

})
