document.getElementById('generateBarcodeBtn').addEventListener('click', function () {
    const barcode = Math.floor(100000000000 + Math.random() * 900000000000).toString();
    document.getElementById('barcode').value = barcode;
});