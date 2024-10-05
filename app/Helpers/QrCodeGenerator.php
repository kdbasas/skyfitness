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
        $qrCodePath = storage_path('app/public/img/qrcode/' . $filename);
        $result->saveToFile($qrCodePath);
    }
}