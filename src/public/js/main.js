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
})
