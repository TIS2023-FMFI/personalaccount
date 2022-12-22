<div id="create-user-modal" class="modal-box">

  <div class="modal">
    <span class="close-modal"><i class="bi bi-x"></i></span>

    <h2>Vytvoriť používateľa</h2>

    <div class="input-box">
      <input type="email" id="create-user-email" placeholder="E-mailová adresa">
      <div class="error-box" id="create-user-email-errors"></div>
    </div>
   
    <button type="button" data-csrf="{{ csrf_token() }}" id="create-user-button">Vytvoriť</button>
    
  </div>

</div>