@include('common.navigation')

<?php
    $from = filter_input(INPUT_GET, 'from', FILTER_SANITIZE_URL);
    $to = filter_input(INPUT_GET, 'to', FILTER_SANITIZE_URL);           
?>

<div class="flex-between">
    <div>
        <h1>{{ $account->title }}</h1>
        <p>{{ $account->sap_id }}</p>
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
        <button type="button" data-account-id="{{ $account->id }}" data-date-errors="{{$errors->first('to')}}" id="filter-operations">Zobraziť</button>
    </div>

    <div>
        <button data-account-id="{{ $account->id }}" type="button" id="operations-export">Export</button>
        <button data-account-id="{{ $account->id }}" id="create_operation" type="button" title="Nová operácia">+</i></button>
    </div>
</div>

@if ($errors->has('to'))
    <div class="error-div" style="width: 70%; margin: 0px 0px 0px 50px">
        <p style="color:red">Dátum "Do:" musí byť rovnaký ako "Od:" alebo neskorší!</p>
    </div>
@endif

<table>
    <tr>
        <th>Poradie</th>
        <th>Názov</th>
        <th>Dátum pridania</th>
        <th>Typ</th>
        <th class="w-100">Skontrolované</th>
        <th class="align-right">Suma</th>
        <th></th>
    </tr>
    @foreach ($operations as $key=>$operation)
        
        <tr>
            <td>{{ $key+1 }}.</td>
            <td>{{ $operation->title }}</td>
            <td>{{ $operation->date }}</td>
            <td>{{ $operation->operationType->name }}</td>
            @if( $operation->checked )
                <td>Áno</td>
            @else
                <td>Nie</td>
            @endif
            @if( $operation->isExpense())
                <td class="align-right" style="color: red;">-{{ $operation->sum }}€</td>
            @else
                <td class="align-right" style="color: green;">{{ $operation->sum }}€</td>
            @endif
            <td>
                <a href="#"><i data-operation-id="{{ $operation->id }}" class="bi bi-info-circle operation-detail" title="Detail operácie"></i></a>
                @if( ! $operation->isLending() )
                    <a href="#"><i data-operation-id="{{ $operation->id }}" data-operation-checked="{{ $operation->checked }}"class="bi bi-check2-all operation-check" title="Označiť/Odznačiť operáciu" ></i></a>
                @endif
                <a href="#"><i data-operation-id="{{ $operation->id }}" class="bi bi-pencil operation-edit" title="Upraviť operáciu"></i></a>
                <a href="#"><i data-operation-id="{{ $operation->id }}" class="bi bi-trash3 operation-delete" title="Zmazať operáciu"></i></a>
            </td>
        </tr>

    @endforeach
</table>

<div class="table-sum">
    <div class="pagination"> {{ $operations->links("pagination::semantic-ui") }} </div>

    <p id="income">Príjmy: <em>{{ $incomes_total }}€</em></p>
    <p id="outcome">Výdavky: <em>{{ $expenses_total }}€</em></p>
    @if( ($incomes_total - $expenses_total) >= 0)
        <p id="total">Rozdiel: <em style="color: green;">{{ $incomes_total - $expenses_total }}€</em></p>
    @else
        <p id="total">Rozdiel: <em style="color: red;">{{ $incomes_total - $expenses_total }}€</em></p>
    @endif
</div>


@include('common.footer')