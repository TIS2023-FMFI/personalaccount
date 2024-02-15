@php use App\Http\Controllers\SapOperationModel; @endphp
<div id="check-operation-modal" class="modal-box">

    <div class="modal">
        <form id="check-operation-form">
            <span class="close-modal"><i class="bi bi-x"></i></span>
            <p>Vyberte operaciu</p>
            @foreach($all as $oper)
                <option>$oper</option>
            @endforeach
            <div>
                <button class="proceed" type="submit" data-csrf="{{ csrf_token() }}" id="check-operation-button">Áno
                </button>
                <button class="cancel" type="button">Nie</button>
            </div>
        </form>
    </div>

</div>
