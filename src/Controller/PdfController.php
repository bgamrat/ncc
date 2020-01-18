<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PdfController {

    private $basePath;

    public function __construct(String $basePath, String $baseDir) {
        $this->basePath = $basePath . '/' . $baseDir . '/';
    }

    public function pdfViewAction(Request $request) {
        $path = $request->get('path');
        return new BinaryFileResponse($this->basePath . $path);
    }

}
