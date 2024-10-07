<?php

namespace App\Helpers;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\ResultInterface;

class QrCodeGenerator
{
    public function generate($data, $filename)
    {
        $qrCode = new QrCode($data);
        $qrCode->setSize(200);
        $foregroundColor = new Color(0, 0, 0);
        $backgroundColor = new Color(255, 255, 255);
        $qrCode->setForegroundColor($foregroundColor);
        $qrCode->setBackgroundColor($backgroundColor);

        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $result = $writer->write($qrCode);

        // Create the directory if it does not exist
        $qrCodePath = storage_path('app/public/img/qrcode');
        if (!is_dir($qrCodePath)) {
            mkdir($qrCodePath, 0777, true);
        }

        // Save the QR code image
        $result->saveToFile($qrCodePath . '/' . $filename);
    }
}