@include('common/navigation')

<div class="flex-between">
    <div>
        <h1>Názov účtu</h1>
        <p>O-06-107/0008-00</p>
    </div>
    <div class="switch-box">
        <p>Výpis účtu</p>
        <label class="switch">
            <input class="toggle-button" checked="true" type="checkbox">
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
        <button id="add_sap_report" type="button">+</i></button>
    </div>
</div>
<table>
    <tr>
        <th>Poradie</th>
        <th>Dátum</th>
        <th></th>
    </tr>
    <tr>
        <td>1.</td>
        <td>12.12.2022</td>
        <td>
            <a href="#"><i class="bi bi-download"></i></a>
            <a href="#"><i class="bi bi-trash3"></i></a>
        </td>
    </tr>
    <tr>
        <td>2.</td>
        <td>12.12.2022</td>
        <td>
            <a href="#"><i class="bi bi-download"></i></a>
            <a href="#"><i class="bi bi-trash3"></i></a>
        </td>
    </tr>
    <tr>
        <td>3.</td>
        <td>12.12.2022</td>
        <td>
            <a href="#"><i class="bi bi-download"></i></a>
            <a href="#"><i class="bi bi-trash3"></i></a>
        </td>
    </tr>
    <tr>
        <td>4.</td>
        <td>12.12.2022</td>
        <td>
            <a href="#"><i class="bi bi-download"></i></a>
            <a href="#"><i class="bi bi-trash3"></i></a>
        </td>
    </tr>
    <tr>
        <td>5.</td>
        <td>12.12.2022</td>
        <td>
            <a href="#"><i class="bi bi-download"></i></a>
            <a href="#"><i class="bi bi-trash3"></i></a>
        </td>
    </tr>
</table>
<div class="pagination">1 <b>2</b> .. 10</div>

@include('common/footer')