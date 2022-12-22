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

                <h1>Prihlásenie</h1>

                <form method="POST" action="/login">
                    @csrf
                    
                    @if ($errors->has('email'))
                        <div class="input-box">
                            <input type="text" name="email" placeholder="E-mailová adresa" style="border-color: red;">
                        </div>
                        <div class="input-box">
                            <input type="password" name="password" placeholder="Prihlasovacie heslo" style="border-color: red;">
                            <div class="error-box">
                                <p>{{ $errors->first('email') }}</p>
                            </div>
                        </div>
                    @else
                        <div class="input-box">
                            <input type="text" name="email" placeholder="E-mailová adresa">
                        </div>
                        <div class="input-box">
                            <input type="password" name="password" placeholder="Prihlasovacie heslo">
                        </div>
                    @endif

                    <button type="submit" class="login-button">Prihlásiť sa</button>
                    <a href="/forgot-password">Zabudli ste heslo?</a>

                </form>
            </div>
        </div>
    </body>
</html>