<div id="create-user-modal" class="modal-box">

  <div class="modal">
    <span class="close-modal"><i class="bi bi-x"></i></span>
    <h2>Vytvoriť používateľa</h2>
    <input type="email" id="create-user-email" placeholder="E-mailová adresa">
    <button type="button" data-csrf="{{ csrf_token() }}" id="create-user-button">Vytvoriť</button>
  </div>

</div>