<?php $this->load->view('admin/layouts/header', ['page_title' => 'Create Role']); ?>

<?php echo form_open('admin/material', ['id' => 'createUserForm']); ?>

<div class="col-md-6">
    <label class="form-label fw-semibold">Material Types <span class="text-danger">*</span></label>
    <select name="material_type" id="type_select" class="form-select" required>
    <option value="none" <?= $material_type == 'none' ? 'selected' : '' ?>>Select Type</option>

    <?php $types = ['paper','glass','stone']; ?>
    <?php foreach ($types as $type): ?>
        <option value="<?= $type ?>">
            <?= htmlspecialchars($type) ?>
        </option>
    <?php endforeach; ?>
</select>
</div>

<div class='mb-3' id='material_options'> 

</div>

<div class="modal fade" id="saveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Verification Pop-up</h6>
            </div>
            <div class="modal-body" id='modal-body'>
                <p>
                    You have selected <strong id='material_type_in_model'></strong> with below options
                </p>
                <ul id='options_list'>

                </ul>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button id="saveBtn" type='submit' class="btn btn-sm btn-primary">Save</button>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="button" id='submit' class="btn btn-primary" disabled>Submit</button>
    <button type="reset" onclick="handletypechange(true)" class="btn btn-secondary">Reset</button>
</div>
<?php echo form_close(); ?>


<?php if ($this->session->flashdata('success')): ?>
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
  <div id="myToast" class="toast text-bg-success border-0">
    <div class="d-flex">
      <div class="toast-body">
        <?= $this->session->flashdata('success') ?>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto"
        data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>
<?php endif;?>

<script>
    const materialTypeSelect = document.getElementById('type_select');
    let selectedOptions = [];
    function handletypechange(isReset){
        let materialType = materialTypeSelect.value;
        if(isReset){
            materialType = 'none';
            selectedOptions = [];
            optionsChange('reset');
        }
        if(materialType === 'none'){
            selectedOptions = [];
            optionsChange('reset');
            const optionsDiv = document.getElementById('material_options');
            optionsDiv.innerHTML = ``;
        }
        if(materialType === 'paper'){
            selectedOptions = [];
            optionsChange('reset');
            const optionsDiv = document.getElementById('material_options');
            optionsDiv.innerHTML = `
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="material_options[]" value="rough_paper"
                    id="rough_paper" onchange="optionsChange('rough_paper')" <?= set_value('rough_paper') ? 'checked' : '' ?>>
                <label class="form-check-label" for="rough_paper">Rough paper</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="material_options[]" value="note_paper"
                    id="note_paper" onchange="optionsChange('note_paper')" <?= set_value('note_paper') ? 'checked' : '' ?>>
                <label class="form-check-label" for="note_paper">Note paper</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="material_options[]" value="wash_paper"
                    id="wash_paper" onchange="optionsChange('wash_paper')" <?= set_value('wash_paper') ? 'checked' : '' ?>>
                <label class="form-check-label" for="wash_paper">Wash paper</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="material_options[]" value="clean_paper"
                    id="clean_paper" onchange="optionsChange('clean_paper')" <?= set_value('clean_paper') ? 'checked' : '' ?>>
                <label class="form-check-label" for="clean_paper">Clean paper</label>
            </div>
            `;
            
        }
        if(materialType === 'glass'){
            selectedOptions = [];
            optionsChange('reset');
            const optionsDiv = document.getElementById('material_options');
            optionsDiv.innerHTML = `
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="material_options[]" value="thin_glass"
                    id="thin_glass" onchange="optionsChange('thin_glass')" <?= set_value('thin_glass') ? 'checked' : '' ?>>
                <label class="form-check-label" for="thin_glass">Thin glass</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="material_options[]" value="thick_glass"
                    id="thick_glass" onchange="optionsChange('thick_glass')" <?= set_value('thick_glass') ? 'checked' : '' ?>>
                <label class="form-check-label" for="thick_glass">Thick glass</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="material_options[]" value="curve_glass"
                    id="curve_glass" onchange="optionsChange('curve_glass')" <?= set_value('curve_glass') ? 'checked' : '' ?>>
                <label class="form-check-label" for="curve_glass">Curve glass</label>
            </div>
            `;
            
        }
        if(materialType === 'stone'){
            selectedOptions = [];
            optionsChange('reset');
            const optionsDiv = document.getElementById('material_options');
            optionsDiv.innerHTML = `
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="material_options[]" value="black_stone"
                    id="black_stone" onchange="optionsChange('black_stone')" <?= set_value('black_stone') ? 'checked' : '' ?>>
                <label class="form-check-label" for="black_stone">Black stone</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="material_options[]" value="white_stone"
                    id="white_stone" onchange="optionsChange('white_stone')" <?= set_value('white_stone') ? 'checked' : '' ?>>
                <label class="form-check-label" for="white_stone">White stone</label>
            </div>
            `;
            
        }
    }
    materialTypeSelect.addEventListener('change',() => handletypechange(false))

    const submitBtn = document.getElementById('submit');

    function optionsChange(value){
        if(value === 'reset'){
            submitBtn.disabled = true;
            return;
        }
        if(selectedOptions.includes(value)){
            selectedOptions = selectedOptions.filter(option => option !== value);
        }else{
            selectedOptions.push(value);
        }
        if(selectedOptions.length > 0){
            submitBtn.disabled = false;
        }else{
            submitBtn.disabled = true;
        }
    }

    
    submitBtn.addEventListener('click',() => {
        let materialType = materialTypeSelect.value;
        const materialTypeForModal =  document.getElementById('material_type_in_model');
        materialTypeForModal.textContent = materialType;

        const optionsList = document.getElementById('options_list');

        optionsList.innerHTML = ``;
        selectedOptions.forEach(option => {
            const listItem = document.createElement('li');
            option = option.replace('_',' ');
            listItem.textContent = option;
            optionsList.appendChild(listItem);
        });
        new bootstrap.Modal(document.getElementById('saveModal')).show();
    })

document.addEventListener("DOMContentLoaded", function () {
    const toast = new bootstrap.Toast(document.getElementById('myToast'));
    toast.show();
});
</script>
<?php $this->load->view('admin/layouts/footer'); ?>