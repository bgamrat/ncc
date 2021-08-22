<?php

namespace App\EventListener;

use eZ\Publish\API\Repository\LocationService;
use EzSystems\EzPlatformContentForms\Event\FormActionEvent;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;

class PublishListener {

    private $locationService;
    private $pdfOnPublish;
    private $router;
    private $basePath;

    public function __construct(String $basePath, UrlAliasRouter $router, LocationService $locationService, Array $pdfOnPublish = []) {
        $this->locationService = $locationService;
        $this->pdfOnPublish = $pdfOnPublish;
        $this->router = $router;
        $this->basePath = $basePath;
    }

    public function onPublishVersionEvent(FormActionEvent $event) {

        $data = $event->getData();
        $contentInfo = $data->contentDraft->versionInfo->contentInfo;
        $locationId = $contentInfo->mainLocationId;

        if (in_array($contentInfo->contentTypeId, $this->pdfOnPublish)) {
            $location = $this->locationService->loadLocation($locationId);
            $url = preg_replace('#^/admin#', '', $this->router->generate($location));
            $pdfFilename = $this->basePath . $url . '.pdf';
            if (is_file($pdfFilename)) {
                unlink($pdfFilename);
            }
        }
    }

}
