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
        $qrCode = $this->createQrCode($data);
        $image = imagecreate(200, 200);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        imagefill($image, 0, 0, $white);
        $this->drawQrCode($image, $qrCode, $black);
        imagepng($image, public_path('img/qrcode/' . $filename));
        imagedestroy($image);
    }

    private function createQrCode($data)
    {
        $qrCode = array();
        $size = 200;
        $margin = 10;
        $qrCodeSize = $size - 2 * $margin;
        $qrCodeData = str_split($data);
        $qrCodeSizeX = $qrCodeSize;
        $qrCodeSizeY = $qrCodeSize;
        for ($y = 0; $y < $qrCodeSizeY; $y++) {
            for ($x = 0; $x < $qrCodeSizeX; $x++) {
                $qrCode[$y][$x] = 0;
            }
        }
        $qrCodeDataIndex = 0;
        for ($y = 0; $y < $qrCodeSizeY; $y++) {
            for ($x = 0; $x < $qrCodeSizeX; $x++) {
                if ($qrCodeDataIndex < count($qrCodeData)) {
                    $qrCode[$y][$x] = ord($qrCodeData[$qrCodeDataIndex]);
                    $qrCodeDataIndex++;
                }
            }
        }
        return $qrCode;
    }

    private function drawQrCode($image, $qrCode, $color)
    {
        $size = 200;
        $margin = 10;
        $qrCodeSize = $size - 2 * $margin;
        $qrCodeSizeX = $qrCodeSize;
        $qrCodeSizeY = $qrCodeSize;
        for ($y = 0; $y < $qrCodeSizeY; $y++) {
            for ($x = 0; $x < $qrCodeSizeX; $x++) {
                if ($qrCode[$y][$x] == 1) {
                    imagefilledrectangle($image, $x + $margin, $y + $margin, $x + $margin + 1, $y + $margin + 1, $color);
                }
            }
        }
    }
}