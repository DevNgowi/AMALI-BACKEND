class ItemFormManager {
    constructor() {
        this.baseUnitSelect = document.getElementById('unit_id');
        this.baseUnitIdInput = document.getElementById('base_unit_id');
        this.conversionTable = document.getElementById('unitsTable')?.getElementsByTagName('tbody')[0];
        this.addButton = document.getElementById('addUnitRow');
        this.units = [];
        
        this.initializeEventListeners();
        this.loadUnits();
    }

    initializeEventListeners() {
        if (this.baseUnitSelect) {
            this.baseUnitSelect.addEventListener('change', () => this.handleBaseUnitChange());
        }

        if (this.addButton) {
            this.addButton.addEventListener('click', () => this.handleAddRow());
        }

        if (this.conversionTable) {
            this.conversionTable.addEventListener('click', (e) => {
                if (e.target.classList.contains('delete-row')) {
                    e.target.closest('tr').remove();
                }
            });

            this.conversionTable.addEventListener('change', (e) => {
                if (e.target.name === 'converted_unit_id[]') {
                    this.updateConversionLabel(e.target);
                }
            });
        }
    }

    loadUnits() {
        if (this.baseUnitSelect) {
            this.units = Array.from(this.baseUnitSelect.options)
                .filter(option => option.value)
                .map(option => ({
                    id: option.value,
                    name: option.text
                }));
        }
    }

    handleBaseUnitChange() {
        const selectedBaseUnit = this.baseUnitSelect.value;
        
        if (this.conversionTable) {
            this.conversionTable.innerHTML = '';
        }
        
        if (this.baseUnitIdInput) {
            this.baseUnitIdInput.value = selectedBaseUnit;
        }

        if (selectedBaseUnit) {
            this.addBaseUnitRow(selectedBaseUnit);
        }
    }

    updateConversionLabel(selectElement) {
        const row = selectElement.closest('tr');
        const conversionInput = row.querySelector('input[name="conversion_factor[]"]');
        const selectedUnit = selectElement.options[selectElement.selectedIndex].text;
        const baseUnit = this.baseUnitSelect.options[this.baseUnitSelect.selectedIndex].text;
        
        if (conversionInput && selectedUnit) {
            conversionInput.setAttribute('placeholder', `${selectedUnit}/${baseUnit}`);
        }
    }

    addBaseUnitRow(baseUnitId) {
        if (!this.conversionTable) return;

        const baseUnit = this.units.find(u => u.id === baseUnitId);
        if (!baseUnit) return;

        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select name="converted_unit_id[]" class="form-control" required disabled>
                    <option value="${baseUnit.id}">${baseUnit.name}</option>
                </select>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" name="conversion_factor[]" class="form-control" value="1" aria-describedby="basic-addon2" readonly required>
                    <span class="input-group-text" id="basic-addon2">${baseUnit.name}</span>
                </div>
            </td>
            <td>
                <input type="number" name="wastage_percentage[]" class="form-control" value="0" required>
            </td>
            
            <td>
                <select name="calculate_stock[]" class="form-control" required>
                    <option value="1" selected>Yes</option>
                    <option value="0">No</option>
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm delete-row" disabled>Delete</button>
            </td>
        `;

        this.conversionTable.appendChild(newRow);
    }

    getUnitsOptionsHtml(excludeUnitId) {
        let options = '<option value="">Select Unit</option>';
        this.units
            .filter(unit => unit.id !== excludeUnitId)
            .forEach(unit => {
                options += `<option value="${unit.id}">${unit.name}</option>`;
            });
        return options;
    }

    handleAddRow() {
        if (!this.conversionTable || !this.baseUnitSelect.value) return;

        const baseUnit = this.baseUnitSelect.options[this.baseUnitSelect.selectedIndex].text;

        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select name="converted_unit_id[]" class="form-control" required>
                    ${this.getUnitsOptionsHtml(this.baseUnitSelect.value)}
                </select>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" name="conversion_factor[]" class="form-control" step="0.01" aria-describedby="basic-addon2" placeholder="Unit/${baseUnit}">
                    <span class="input-group-text" id="basic-addon2">${baseUnit}</span>
                </div>
            </td>
            <td>
                <input type="number" name="wastage_percentage[]" class="form-control" step="0.01" value="0" required>
            </td>
            
            <td>
                <select name="calculate_stock[]" class="form-control" required>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm delete-row">Delete</button>
            </td>
        `;

        this.conversionTable.appendChild(newRow);

        const newSelect = newRow.querySelector('select[name="converted_unit_id[]"]');
        newSelect.addEventListener('change', () => this.updateConversionLabel(newSelect));
    }
}

document.addEventListener('DOMContentLoaded', function() {
    window.itemFormManager = new ItemFormManager();
});