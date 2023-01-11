@include('common.navigation')

<h1>Moje účty</h1>
<div class="accounts_box">
    
    <?php 
        foreach ($accounts as $account) {
            $account_balance = $account->getBalance();
            $account_id = $account->id;
            $account_title = $account->title;
            $color_of_balance = 'red';
            if($account_balance >= 0){
                $color_of_balance = 'green';
            }
            echo <<<EOL
                <div class="account_box">
                    <div data-id="$account_id" class="account">
                        <h2>$account_title</h2>
                        <p>Zostatok na účte: <em style="color: $color_of_balance";>$account_balance €</em></p>
                    </div>
                    <i data-id="$account_id" class="bi bi-pencil edit_account"></i>
                    <i data-id="$account_id" class="bi bi-trash3 delete_account"></i>
                </div>
                EOL;
        }
    ?>

    <div class="add_account_button">
        <i class="bi bi-plus"></i>
    </div>
</div>

@include('common.footer')