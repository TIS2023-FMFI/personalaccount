<div id="create-operation-modal" class="modal-box">

  <div class="modal">
    <span class="close-modal"><i class="bi bi-x"></i></span>

    <h2>Pridať operáciu</h2>

    <form id="create-operation-form">

      <div class="radio-buttons-box">
          <div>
            <input class="operation_type" id="income_choice" name="operation_type" type="radio" value="income" checked>&ensp;
            <label for="income_choice"><i>Príjem</i></label>
          </div>
          <div>
            <input class="operation_type" id="expense_choice" name="operation_type" type="radio" value="expense">&ensp;
            <label for="expense_choice"><i>Výdavok</i></label>
          </div>
      </div>

      <div class="input-box">
        <div class="field">
          <select id="operation_choice" name="typ" id="add-operation-type">
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
          <label for="add-operation-type">Typ operácie</label>
        </div>
        <div class="error-box" id="add-operation-type-errors"></div>
      </div>

      <div class="input-box">
        <div class="field">
          <input type="text" id="add-operation-name">
          <label for="add-operation-name">Názov</label>
        </div>
        <div class="error-box" id="add-operation-title-errors"></div>
      </div>

      <div class="input-box">
        <div class="field">
          <input type="text" id="add-operation-subject">
          <label for="add-operation-subject">Subjekt</label>
        </div>
        <div class="error-box" id="add-operation-subject-errors"></div>
      </div>

      <div class="input-box">
        <div class="field">
          <input type="text" id="add-operation-sum">
          <label for="add-operation-sum">Suma</label>
        </div>
        <div class="error-box" id="add-operation-sum-errors"></div>
      </div>

      <div class="input-box">
        <div class="field">
          <input type="date" id="add-operation-to">
          <label for="add-operation-to">Splatné do</label>
        </div>
        <div class="error-box" id="add-operation-date-errors"></div>
      </div>

      <div class="input-box">
        <div class="field">
          <input type="file" id="operation-file" name="" accept=".doc, .docx, .pdf, .txt">
          <label for="operation-file">Príloha</label>
        </div>
        <div class="error-box" id="add-operation-attachment-errors"></div>
      </div>

      <button type="submit" data-csrf="{{ csrf_token() }}"  id="create-operation-button">Uložiť</button>

    </form>
  </div>

</div>