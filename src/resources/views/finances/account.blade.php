@include('common.navigation')

<div class="flex-between">
    <div>
        <h1>Názov účtu</h1>
        <p>O-06-107/0008-00</p>
    </div>
    <div class="switch-box">
        <p>Výpis účtu</p>
        <label class="switch">
            <input class="toggle-button" type="checkbox">
            <span class="slider round"></span>
        </label>
        <p>SAP</p>
    </div>
</div>
<div class="filter-box">
    <div>
        <label>Od:</label><input type="date"></input>
        <label>Do:</label><input type="date"></input>
        <button type="button">Zobraziť</button>
    </div>
    <div>
        <button type="button">Export</button>
        <button id="create_operation" type="button">+</i></button>
    </div>
</div>
<table>
    <tr>
        <th>Poradie</th>
        <th>Názov</th>
        <th>Dátum pridania</th>
        <th>Typ</th>
        <th>Skontrolované</th>
        <th>Suma</th>
        <th></th>
    </tr>
    <tr>
        <td>1.</td>
        <td>Lorem ipsum</td>
        <td>12.12.2022</td>
        <td>Grant</td>
        <td>Nie</td>
        <td style="color: green;">123€</td>
        <td>
            <a href="#"><i class="bi bi-info-circle"></i></a>
            <a href="#"><i class="bi bi-check2-all"></i></a>
            <a href="#"><i class="bi bi-pencil"></i></a>
            <a href="#"><i class="bi bi-trash3"></i></a>
        </td>
    </tr>
    <tr>
        <td>2.</td>
        <td>Lorem ipsum</td>
        <td>12.12.2022</td>
        <td>Grant</td>
        <td>Áno</td>
        <td style="color: green;">3429€</td>
        <td>
            <a href="#"><i class="bi bi-info-circle"></i></a>
            <a href="#"><i class="bi bi-check2-all"></i></a>
            <a href="#"><i class="bi bi-pencil"></i></a>
            <a href="#"><i class="bi bi-trash3"></i></a>
        </td>
    </tr>
    <tr>
        <td>3.</td>
        <td>Lorem ipsum</td>
        <td>12.12.2022</td>
        <td>Nákup</td>
        <td>Áno</td>
        <td style="color: red;">-564€</td>
        <td>
            <a href="#"><i class="bi bi-info-circle"></i></a>
            <a href="#"><i class="bi bi-check2-all"></i></a>
            <a href="#"><i class="bi bi-pencil"></i></a>
            <a href="#"><i class="bi bi-trash3"></i></a>
        </td>
    </tr>
    <tr>
        <td>4.</td>
        <td>Lorem ipsum</td>
        <td>12.12.2022</td>
        <td>Pôžička</td>
        <td>Áno</td>
        <td style="color: red;">-1203€</td>
        <td>
            <a href="#"><i class="bi bi-info-circle"></i></a>
            <a href="#"><i class="bi bi-check2-all"></i></a>
            <a href="#"><i class="bi bi-pencil"></i></a>
            <a href="#"><i class="bi bi-trash3"></i></a>
        </td>
    </tr>
    <tr>
        <td>5.</td>
        <td>Lorem ipsum</td>
        <td>12.12.2022</td>
        <td>Pôžička</td>
        <td>Áno</td>
        <td style="color: green;">123€</td>
        <td>
            <a href="#"><i class="bi bi-info-circle"></i></a>
            <a href="#"><i class="bi bi-check2-all"></i></a>
            <a href="#"><i class="bi bi-pencil"></i></a>
            <a href="#"><i class="bi bi-trash3"></i></a>
        </td>
    </tr>
</table>
<div class="pagination">1 <b>2</b> .. 10</div>
<div class="table-sum">
    <p id="income">Príjmy: <em>3675€</em></p>
    <p id="outcome">Výdavky: <em>1767€</em></p>
    <p id="total">Rozdiel: <em style="color: green;">1908€</em></p>
</div>


@include('common.footer')