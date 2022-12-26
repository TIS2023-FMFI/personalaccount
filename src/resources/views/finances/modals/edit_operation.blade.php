<div id="edit-operation-modal" class="modal-box">

  <div class="modal">
    <span class="close-modal"><i class="bi bi-x"></i></span>

    <h2>Upraviť operáciu</h2>

    <div class="radio-buttons-box">
        <div>
          <input class="operation_type" id="edit_income_choice" name="edit_operation_type" type="radio" value="income" checked>&ensp;
          <label for="edit_income_choice"><i>Príjem</i></label>
        </div>
        <div>
          <input class="operation_type" id="edit_expense_choice" name="edit_operation_type" type="radio" value="expense">&ensp;
          <label for="edit_expense_choice"><i>Výdavok</i></label>
        </div>
    </div>

    <div class="input-box">
      <div class="field">
        <select id="operation_choice" name="typ" id="edit-operation-type">
          <option value="default_opt">Vyberte typ operácie</option>

          <option class="expense_opt" value="">Náklady na služobnú cestu</option>
          <option class="expense_opt" value="">Malý nákup</option>
          <option class="expense_opt" value="">Nákup na faktúru</option>
          <option class="expense_opt" value="">Nákup z Marquetu</option>
          <option class="expense_opt" value="lending_to">Pôžička pre niekoho</option>

          <option class="income_opt" value="">Zo služby s faktúrou</option>
          <option class="income_opt" value="">Projektový grant</option>
          <option class="income_opt" value="lending_from">Pôžička od niekoho</option>
          <option class="income_opt" value="return_of_lending">Splatenie pôžičky od niekoho</option>

        </select>
        <label for="edit-operation-type">Typ operácie</label>
      </div>
    </div>

    <div class="input-box">
      <div class="field">
        <input type="text" id="edit-operation-name" placeholder="...">
        <label for="edit-operation-name">Názov</label>
      </div>
    </div>

    <div class="input-box">
      <div class="field">
        <input type="text" id="edit-operation-subject" placeholder="...">
        <label for="edit-operation-subject">Subjekt</label>
      </div>
    </div>

    <div class="input-box">
      <div class="field">
        <input type="text" id="edit-operation-sum" placeholder="...">
        <label for="edit-operation-sum">Suma</label>
      </div>
    </div>

    <div class="input-box edit_lending_opt">
      <div class="field">
        <input type="date" id="edit-operation-to">
        <label for="edit-operation-to">Splatné do</label>
      </div>
    </div>

    <div class="input-box">
      <div class="field">
        <input type="file" id="operation_file" name="" accept=".doc, .docx, .pdf">
        <label for="operation_file">Príloha</label>
      </div>
    </div>

    <button type="button" class="create">Uložiť</button>

  </div>

</div>