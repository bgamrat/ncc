<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PdfController extends AbstractController {

    private $baseDir;

    public function __construct(String $baseDir) {
        $this->baseDir = $baseDir;
    }

    public function pdfViewAction(Request $request) {
        $path = $request->get('path');
        $filename = $this->baseDir . '/' . $path;
        
        if (!is_file($filename)) {
           return $this->redirect(preg_replace('#^([^\.]+)\.pdf$#','/$1',$path));
        }
        return new BinaryFileResponse($this->baseDir . '/' . $path);
    }

}
