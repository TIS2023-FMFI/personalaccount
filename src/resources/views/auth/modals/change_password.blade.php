<div id="change-pass-modal" class="modal-box">

  <div class="modal">

    <span class="close-modal"><i class="bi bi-x"></i></span>

    <h2>Zmena hesla</h2>

    <form id="change-pass-form">
      <div class="input-box">
        <input type="password" id="change-pass-old" placeholder="Aktuálne heslo">
        <div class="error-box" id="change-pass-old-errors"></div>
      </div>

      <div class="input-box">
        <input type="password" id="change-pass-new1" placeholder="Nové heslo">
        <div class="error-box" id="change-pass-new1-errors"></div>
      </div>

      <div class="input-box">
        <input type="password" id="change-pass-new2" placeholder="Zopakujte heslo">
        <div class="error-box" id="change-pass-new2-errors"></div>
      </div>

      <button type="submit" data-csrf="{{ csrf_token() }}" id="change-pass-button">Uložiť</button>
    </form>
  </div>

</div>