<div id="edit-operation-modal" class="modal-box">

  <div class="modal">
    <span class="close-modal"><i class="bi bi-x"></i></span>

    <h2>Upraviť operáciu</h2>

    <form id="edit-operation-form">

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
          <select id="operation_edit_choice" name="typ" id="edit-operation-type">
            <option value="default_opt">Vyberte typ operácie</option>

            <option class="expense_opt" value="1">Náklady na služobnú cestu</option>
            <option class="expense_opt" value="2">Malý nákup</option>
            <option class="expense_opt" value="3">Nákup na faktúru</option>
            <option class="expense_opt" value="4">Nákup z Marquetu</option>
            <option class="expense_opt" value="5">Pôžička pre niekoho</option>

            <option class="income_opt" value="6">Zo služby s faktúrou</option>
            <option class="income_opt" value="7">Projektový grant</option>
            <option class="income_opt" value="8">Pôžička od niekoho</option>
            <option class="income_opt" value="9">Splatenie pôžičky od niekoho</option>

          </select>
          <label for="edit-operation-type">Typ operácie</label>
        </div>
        <div class="error-box" id="edit-operation-type-errors"></div>
      </div>

      <div class="input-box">
        <div class="field">
          <input type="text" id="edit-operation-name">
          <label for="edit-operation-name">Názov</label>
        </div>
        <div class="error-box" id="edit-operation-title-errors"></div>
      </div>

      <div class="input-box">
        <div class="field">
          <input type="text" id="edit-operation-subject">
          <label for="edit-operation-subject">Subjekt</label>
        </div>
        <div class="error-box" id="edit-operation-subject-errors"></div>
      </div>

      <div class="input-box">
        <div class="field">
          <input type="text" id="edit-operation-sum">
          <label for="edit-operation-sum">Suma</label>
        </div>
        <div class="error-box" id="edit-operation-sum-errors"></div>

      </div>

      <div class="input-box edit_lending_opt">
        <div class="field">
          <input type="date" id="edit-operation-to">
          <label for="edit-operation-to">Splatné do</label>
        </div>
        <div class="error-box" id="edit-operation-date-errors"></div>
      </div>

      <div class="input-box">
        <div class="field">
        <input type="file" id="edit-operation-file" name="" accept=".doc, .docx, .pdf, .txt">
          <label for="edit-operation_file">Príloha</label>
        </div>
        <div class="error-box" id="edit-operation-attachment-errors"></div>
      </div>

      <button type="submit" data-csrf="{{ csrf_token() }}"  id="edit-operation-button">Uložiť</button>

    </form>
  </div>

</div>