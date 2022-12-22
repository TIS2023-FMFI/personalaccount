<div id="create-operation-modal" class="modal-box">

  <div class="modal">
    <span class="close-modal"><i class="bi bi-x"></i></span>
    <h2>Pridať operáciu</h2>
    <div>
      <input class="operation_type" id="income_choice" name="operation_type" type="radio" value="income" checked>
      <label for="income_choice"><i>Príjem</i></label>
      <input class="operation_type" id="expense_choice" name="operation_type" type="radio" value="expense">
      <label for="expense_choice"><i>Výdavok</i></label>
    </div>
    <select id="operation_choice" name="typ">
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
    <input type="text" placeholder="Názov">
    <input type="text" placeholder="Subjekt">
    <input type="text" placeholder="Suma">
    <input type="text" placeholder="Názov">
    <label class="lending_opt">Splatné do:</label>
    <input class="lending_opt" type="date" placeholder="dd.mm.yyyy">
    <label>Doklad:</label>
    <input type="file" id="operation_file" name="" accept=".doc, .docx, .pdf">

    <button type="button" class="create">Uložiť</button>

  </div>

</div>