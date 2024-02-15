@php use App\Http\Controllers\SapOperationModel; @endphp
@include('common.navigation')

<?php
$from = filter_input(INPUT_GET, 'from', FILTER_SANITIZE_URL);
$to = filter_input(INPUT_GET, 'to', FILTER_SANITIZE_URL);
?>

<div class="flex-between">
    <div class="main_info">
        <a href={{ route('home') }} class="return_home"><i class="bi bi-chevron-left"></i> Späť na prehľad</a>
        <h1>{{ $account_title }}</h1>
        <label for="sap-id-detail"><b>SAP ID:</b></label>
        <p id="sap-id-detail">{{ $account->sap_id }}</p>
    </div>
    <div class="switch-box">
        <p>Výpis účtu</p>
        <label class="switch">
            <input data-account-id="{{ $account->id }}" class="toggle-button" type="checkbox">
            <span class="slider round"></span>
        </label>
        <p>SAP</p>
    </div>
</div>

<div class="filter-box">

    <div>

        <label>Od:</label><input type="date" id="filter-operations-from" value="<?php echo $from ?>"></input>
        <label>Do:</label><input type="date" id="filter-operations-to" value="<?php echo $to ?>"></input>
        <button type="button" data-account-id="{{ $account->id }}" data-date-errors="{{$errors->first('to')}}"
                id="filter-operations">Filtrovať
        </button>
        <button data-account-id="{{ $account->id }}" type="button" id="operations-export">Exportovať</button>
    </div>

    <div>
        <button data-account-id="{{ $account->id }}" data-csrf="{{ csrf_token() }}" id="create_operation" type="button"
                title="Nová operácia"><i>+</i></button>
    </div>
</div>

@if ($errors->has('to'))
    <div class="error-div" style="width: 70%; margin: 0px 0px 0px 50px">
        <p style="color:red">{{ $errors->first('to') }}</p>
    </div>
@endif

<table>
    <tr>
        <th>Poradie</th>
        <th>Názov</th>
        <th>Dátum</th>
        <th>Typ</th>
        <th class="align-right">Suma</th>
        <th>Skontrolovane</th>
        <th></th>
    </tr>

    @foreach ($operations as $key=>$operation)

        <tr>
            <td>{{ ($operations->currentPage() - 1) * $operations->perPage() + $key + 1}}.</td>
            <td>{{ $operation->title }}</td>
            <td>{{ $operation->date->format('d.m.Y') }}</td>
            <td>{{ $operation->subject }}</td>

            @if( $operation->sum >= 0)
                <td class="align-right" style="color: red;">{{ -$operation->sum }}€</td>
            @else
                <td class="align-right" style="color: green;">{{ -$operation->sum }}€</td>
            @endif
            <td>Nie</td>
        </tr>

    @endforeach
</table>

<div class="table-sum">
    <div class="pagination"> {{ $operations->links("pagination::semantic-ui") }} </div>

    <p id="income">Príjmy: <em>{{ $incomes_total }}€</em></p>
    <p id="outcome">Výdavky: <em>{{ $expenses_total }}€</em></p>
    @if( ($incomes_total + $expenses_total) >= 0)
        <p id="total">Rozdiel: <em style="color: green;">{{ $incomes_total + $expenses_total }}€</em></p>
    @else
        <p id="total">Rozdiel: <em style="color: red;">{{ $incomes_total + $expenses_total }}€</em></p>
    @endif
    <p id="account-balance">Celkový zostatok na účte: <em>{{ $account_balance }}€</em></p>
</div>


@include('common.footer')
