<div id="create-operation-modal" class="modal-box">

  <div class="modal">
    <span class="close-modal"><i class="bi bi-x"></i></span>
    <h2>Pridať operáciu</h2>
    <div>
      <input class="operation_type" name="operation_type" type="radio" value="income"><label>Príjem</label>
      <input class="operation_type" name="operation_type" type="radio" value="expense"><label>Výdavok</label>

    </div>
    <select id="operation_choice" name="typ">
      <option value="default_opt">Vyberte typ operácie</option>

      <option class="expense_opt" value="">Travel reimbursement</option>
      <option class="expense_opt" value="">Small purchase</option>
      <option class="expense_opt" value="">Invoice purchase</option>
      <option class="expense_opt" value="">Marquet</option>
      <option class="expense_opt" value="">Lending to</option>

      <option class="income_opt" value="">From service with invoice</option>
      <option class="income_opt" value="">Project grant</option>
      <option class="income_opt" value="">Lending from</option>
      <option class="income_opt" value="">Return of lending</option>

    </select>
    <input type="text" placeholder="Názov">
    <input type="text" placeholder="Subjekt">
    <input type="text" placeholder="Suma">
    <input type="text" placeholder="Názov">
    <label>Splatné do:</label><input type="date" placeholder="dd.mm.yyyy">
    <input type="file" id="operation_file" name="" accept=".doc, .docx, .pdf">

    <button type="button" class="create">Uložiť</button>

  </div>

</div>