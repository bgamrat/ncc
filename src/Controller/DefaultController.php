<?php

namespace App\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use eZ\Publish\Core\Repository\Values\Content\Location;

class DefaultController extends Controller {

    private $locationService;
    private $contentService;
    private $searchService;

    public function __construct(LocationService $locationService, ContentService $contentService, SearchService $searchService) {
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->searchService = $searchService;
    }

    public function courseViewEnhancedAction(ContentView $view) {
        $location = $view->getLocation();
        $view->addParameters($this->_getRelated($location));
        return $view;
    }

    public function syllabusViewEnhancedAction(ContentView $view,
            int $availableSupportServicesId, int $collegePoliciesId, int $gradingSchemeId) {
        $location = $view->getLocation();
        $content = $view->getContent();
        $course = $content->getField('course');
        $courseContent = $this->contentService->loadContent($course->value->destinationContentId);
        $courseContentInfo = $courseContent->getVersionInfo()->getContentInfo();
        $courseLocation = $this->locationService->loadLocation($courseContentInfo->mainLocationId);
        $parameters = $this->_getRelated($courseLocation);
        $view->addParameters($parameters);
        $view->addParameters([
            'availableSupportServices' => $this->contentService->loadContent($availableSupportServicesId),
            'collegePolicies' => $this->contentService->loadContent($collegePoliciesId),
            'departmentPolicies' => $this->contentService->loadContent($parameters['departmentPoliciesId']),
            'gradingScheme' => $this->contentService->loadContent($gradingSchemeId),
            'courseContentInfo' => $courseContentInfo,
            'course' => $courseContent
        ]);
        return $view;
    }

    private function _getRelated(Location $location) {
        $rootLocationId = (string) $this->getConfigResolver()->getParameter('content.tree_root.location_id');
        $locationIds = array_reverse(explode('/', trim($location->pathString, '/')));
        $i = 0;
        while ($locationIds[$i] !== $rootLocationId) {
            $node = $this->locationService->loadLocation($locationIds[$i]);
            $contentId = $node->getContent()->id;
            $content = $this->contentService->loadContent($contentId);
            switch ($content->getContentType()->getName()) {
                case 'Program':
                    $programId = $locationIds[$i];
                    break;
                case 'Department':
                    $departmentId = $locationIds[$i];
                    break;
            }
            $i++;
        }
        $query = new LocationQuery();
        $query->filter = new Criterion\LogicalAnd([
            new Criterion\ParentLocationId($departmentId),
            new Criterion\Field('title', Operator::EQ, 'Department Policies')
                ]
        );
        $results = $this->searchService->findContentInfo($query);
        $items = [];
        foreach ($results->searchHits as $searchHit) {
            $items[] = $searchHit;
        }
        $departmentPoliciesId = $items[0]->valueObject->id;

        return ['departmentId' => $departmentId, 'programId' => $programId, 'departmentPoliciesId' => $departmentPoliciesId];
    }

}
