<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PdfController {

    private $baseDir;

    public function __construct(String $baseDir) {
        $this->baseDir = $baseDir;
    }

    public function pdfViewAction(Request $request) {
        $path = $request->get('path');
        return new BinaryFileResponse($this->baseDir . '/' . $path);
    }

}
