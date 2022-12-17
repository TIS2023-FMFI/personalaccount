<?php

return [

    'login-link' => [
        'subject' => 'Prihlasovacia linka pre :appName',
        'content' => <<<HERE
            Dobrý deň,
            
            nedávno ste nás žiadali o zaslanie prihlasovacej linky za účelom prihlásenia sa do Vášho účtu v aplikácii :appName.
            Vygenerovanú linku môžete nájsť nižšie, avšak dbajte na to, že jej platnosť končí :validUntil.
            
            :url
            
            S pozdravom
            Váš :appName tím
            HERE,
    ]

];
