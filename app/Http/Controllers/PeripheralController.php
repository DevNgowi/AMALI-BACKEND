<?php

namespace App\Http\Controllers;

use App\Models\PrinterSetting;
use App\Models\VirtualDevice;
use Illuminate\Http\Request;


class PeripheralController extends Controller
{
    public function indexPeripheralSetting()
    {

        $peripheral_devices = VirtualDevice::all();

        return view('Sales.peripheral_settings.index', compact('peripheral_devices'));
    }

    public function getSettings($id)
    {
        // Fetch the peripheral device by ID
        $peripheralDevice = VirtualDevice::findOrFail($id);

        // Check if there are existing settings for the device
        $printerSettings = PrinterSetting::where('virtual_device_id', $id)->first();

        // Return the HTML for the settings form
        return response()->json([
            'success' => true,
            'html' => view('settings.peripheral_setting.index', compact('peripheralDevice', 'printerSettings'))->render(),
        ]);
    }

   

    public function updatePeripheralSetting(Request $request, $id)
    {
        $request->validate([
            'printer_name' => 'required|string|max:255',
            'printer_ip' => 'required|ip',
            'printer_type' => 'required|string',
            'paper_size' => 'nullable'
        ]);


        // Update or create printer settings

        PrinterSetting::updateOrCreate(
            ['virtual_device_id' => $id],
            $request->only('printer_name', 'printer_ip', 'printer_type', 'paper_size')
        );
        return redirect()->back()->with('success', 'Printer settings updated successfully!');

    }
}
