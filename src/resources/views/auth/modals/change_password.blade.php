<div id="change-pass-modal" class="modal-box">

  <div class="modal">
    <span class="close-modal"><i class="bi bi-x"></i></span>
    <h2>Zmena hesla</h2>
    <input type="password" id="change-pass-old" placeholder="Aktuálne heslo">
    <input type="password" id="change-pass-new1" placeholder="Nové heslo">
    <input type="password" id="change-pass-new2" placeholder="Zopakujte heslo">
    <button type="button" data-csrf="{{ csrf_token() }}" id="change-pass-button">Uložiť</button>
  </div>

</div>