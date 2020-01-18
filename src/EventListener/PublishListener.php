<?php

namespace App\EventListener;

use eZ\Publish\API\Repository\LocationService;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use Knp\Snappy\Pdf;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Symfony\Component\HttpFoundation\Request;

class PublishListener {

    private $locationService;
    private $pdfOnPublish;
    private $knpSnappyPdf;
    private $router;
    private $basePath;

    public function __construct(String $basePath, String $baseDir, UrlAliasRouter $router, LocationService $locationService, Array $pdfOnPublish = [], Pdf $knpSnappyPdf) {
        $this->locationService = $locationService;
        $this->pdfOnPublish = $pdfOnPublish;
        $this->knpSnappyPdf = $knpSnappyPdf;
        $this->router = $router;
        $this->basePath = $basePath . '/' . $baseDir;
    }

    public function onPublishVersionEvent(FormActionEvent $event) {

        $data = $event->getData();
        $contentInfo = $data->contentDraft->versionInfo->contentInfo;
        $locationId = $contentInfo->mainLocationId;
        $contentObjectId = $contentInfo->id;
        $parameters = [
        ];

        if (in_array($contentInfo->contentTypeId, $this->pdfOnPublish)) {
            $location = $this->locationService->loadLocation($locationId);
            $content = $location->getContent();

            $url = preg_replace('#^/admin#', '', $this->router->generate($location));

            $html = Request::create($url);

            $pdfFilename = $this->basePath . $url . '.pdf';

            if (is_file($pdfFilename)) {
                unlink($pdfFilename);
            }
            $this->knpSnappyPdf->generateFromHtml(
                    $html->getContent(),
                    $pdfFilename
            );
        }
    }

}
