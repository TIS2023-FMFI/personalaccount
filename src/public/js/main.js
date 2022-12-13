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

})
