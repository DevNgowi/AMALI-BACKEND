<div class="settings_form">
    <div class="settings_header">
        <h5 class="settings_title">Printer Settings</h5>
    </div>
    <div class="settings_body">
        <form action="{{ route('update_peripheral_setting', $peripheralDevice->id ?? '') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="input_group">
                <label class="input_label" for="printer_name">Printer Name</label>
                <input type="text" class="input_field" id="printer_name" name="printer_name"
                    value="{{ old('printer_name', $printerSettings->printer_name ?? '') }}" required>
            </div>
            
            <div class="input_group">
                <label class="input_label" for="printer_ip">Printer IP Address</label>
                <input type="text" class="input_field" id="printer_ip" name="printer_ip"
                    value="{{ old('printer_ip', $printerSettings->printer_ip ?? '') }}" required>
            </div>
            
            <div class="input_group">
                <label class="input_label" for="paper_size">Paper Size (inches)</label>
                <select class="input_field select_field" id="paper_size" name="paper_size" required>
                    <option value="">Select Paper Size</option>
                    @foreach(['1', '1.5', '2', '2.5', '3', '3.5', '4'] as $size)
                        <option value="{{ $size }}"
                            {{ isset($printerSettings) && $printerSettings->paper_size == $size ? 'selected' : '' }}>
                            {{ $size }} inch{{ $size != '1' ? 'es' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="input_group">
                <label class="input_label" for="printer_type">Printer Type</label>
                <select class="input_field select_field" id="printer_type" name="printer_type" required>
                    @foreach(['inkjet' => 'Inkjet', 'laser' => 'Laser', 'dot_matrix' => 'Dot Matrix'] as $value => $label)
                        <option value="{{ $value }}"
                            {{ isset($printerSettings) && $printerSettings->printer_type == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="save_button">Save Settings</button>
        </form>
    </div>
</div>