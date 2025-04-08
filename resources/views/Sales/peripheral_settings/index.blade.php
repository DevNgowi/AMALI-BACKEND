@extends('layouts.sales')

@section('content')

<div class="sidebar_setting">
    <div class="peripheral_device">
        <ul class="peripheral_device_list">
            @foreach ($peripheral_devices as $index => $peripheral_device)
                <li class="peripheral_device_list_item">
                    <button class="btn btn-success btn-lg">
                        <i class="fas fa-cog text-lg"></i>
                    </button>
                    <a href="#" onclick="loadPeripheralDeviceSetting('{{ $peripheral_device->id }}')">
                        {{ strtoupper($peripheral_device->name) }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    <p class="no-record" style="display: none;">No Record Found!</p>
</div>

<div class="peripheral_setting_container" id="peripheral_setting_container">
    <!-- Setting container will be loaded dynamically -->
    <div id="setting-container">
        <p>Loading setting container...</p>
    </div>
</div>

<script>
  function loadPeripheralDeviceSetting(deviceId) {
    // Show loading message
    document.getElementById('setting-container').innerHTML = '<p>Loading settings...</p>';

    // Make an AJAX request to fetch the settings
    fetch(`/settings/peripheral_setting/${deviceId}/settings`)
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => { throw new Error(text); });
            }
            return response.json();
        })
        .then(data => {
            // Check if data exists
            if (data.success) {
                // Load the settings form into the container
                document.getElementById('setting-container').innerHTML = data.html;
            } else {
                document.getElementById('setting-container').innerHTML = '<p>No settings found for this device.</p>';
            }
        })
        .catch(error => {
            console.error('Error fetching settings:', error);
            document.getElementById('setting-container').innerHTML = '<p>Error loading settings: ' + error.message + '</p>';
        });
}
</script>

@endsection