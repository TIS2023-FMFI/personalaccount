<div id="create-operation-modal" class="modal-box">

  <div class="modal">
    <span class="close-modal"><i class="bi bi-x"></i></span>

    <h2>Pridať operáciu</h2>

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
        <label for="add-operation-type">Typ operácie</label>
      </div>
    </div>

    <div class="input-box">
      <div class="field">
        <input type="text" id="add-operation-name">
        <label for="add-operation-name">Názov</label>
      </div>
    </div>

    <div class="input-box">
      <div class="field">
        <input type="text" id="add-operation-subject">
        <label for="add-operation-subject">Subjekt</label>
      </div>
    </div>

    <div class="input-box">
      <div class="field">
        <input type="text" id="add-operation-sum">
        <label for="add-operation-sum">Suma</label>
      </div>
    </div>

    <div class="input-box">
      <div class="field">
        <input type="date" id="add-operation-to">
        <label for="add-operation-to">Splatné do</label>
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