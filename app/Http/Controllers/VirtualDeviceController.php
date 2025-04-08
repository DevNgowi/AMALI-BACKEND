<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVirtualDeviceRequest;
use App\Http\Requests\UpdateVirtualDeviceRequest;
use App\Models\VirtualDevice;
use Illuminate\Http\Request;


class VirtualDeviceController extends Controller
{
    public function indexVirtualDevice()
    {
        $virtual_devices = VirtualDevice::all();
        return view('settings.virtual_devices.index', compact('virtual_devices'));
    }

    public function storeVirtualDevice(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:virtual_devices,name',
        ]);

        VirtualDevice::create([
            'name' => $validatedData['name'],
        ]);

        session()->flash('success', 'Virtual device created successfully!');
        return redirect()->route('list_virtual_devices');
    }

    public function updateVirtualDevice(Request $request, $id) {
        $virtual_device = VirtualDevice::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:virtual_devices,name,' . $virtual_device->id,
        ]);

        $virtual_device->update([
            'name' => $validatedData['name'],
        ]);

        session()->flash('success', 'Virtual device updated successfully!');
        return redirect()->route('list_virtual_devices');
    }

    public function deleteVirtualDevice($id) {
        $virtual_device = VirtualDevice::findOrFail($id);
        $virtual_device->delete();

        session()->flash('success', 'Virtual device deleted successfully!');
        return redirect()->route('list_virtual_devices');
    }
}