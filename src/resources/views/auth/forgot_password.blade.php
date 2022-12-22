<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="icon" type="image/x-icon" href="/images/credit-card-fill.svg">
        <link href="/css/main.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="/js/main.js" rel="stylesheet"></script>
        <title>Prihlásenie</title>
    </head>
    <body>
        <div class="login-box">
            <div class="login">
                <h1>Zabudnuté heslo</h1>
                <input type="text" id="forgot-pass-email" placeholder="E-mailová adresa">
                <button type="button" data-csrf="{{ csrf_token() }}" class="forgot-pass-button">Odoslať</button>
            </div>
        </div>
    </body>
</html>