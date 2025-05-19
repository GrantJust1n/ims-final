import React, { useEffect } from 'react';
import { Html5QrcodeScanner } from 'html5-qrcode';

const Scanner = () => {
  useEffect(() => {
    const scanner = new Html5QrcodeScanner(
      "reader",
      { fps: 10, qrbox: 300 },
      false
    );

    scanner.render(
      (decodedText) => {
        alert(`Scanned code: ${decodedText}`);
        scanner.clear();
      },
      (error) => {
        console.warn(`Scan error: ${error}`);
      }
    );

    return () => {
      scanner.clear().catch(() => {});
    };
  }, []);

  return (
    <div className="flex flex-col items-center justify-center min-h-screen bg-gray-100 p-4">
      <h1 className="text-3xl font-bold mb-6">Barcode & QR Code Scanner</h1>
      <p className="mb-4 text-gray-700 max-w-md text-center">
        Point your camera at a barcode or QR code to scan. Make sure to allow camera access when prompted.
      </p>
      <div
        id="reader"
        className="w-full max-w-md rounded-lg shadow-lg border border-gray-300 bg-white"
        style={{ height: 350 }}
      ></div>
    </div>
  );
};

export default Scanner;
