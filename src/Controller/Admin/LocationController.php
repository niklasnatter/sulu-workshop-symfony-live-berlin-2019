<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\DoctrineListRepresentationFactory;
use App\Entity\Location;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Sulu\Component\Rest\RestController;
use Symfony\Component\HttpFoundation\Response;

class LocationController extends RestController implements ClassResourceInterface
{
    /**
     * @var DoctrineListRepresentationFactory
     */
    private $doctrineListRepresentationFactory;

    public function __construct(DoctrineListRepresentationFactory $doctrineListRepresentationFactory)
    {
        $this->doctrineListRepresentationFactory = $doctrineListRepresentationFactory;
    }

    public function cgetAction(): Response
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            Location::RESOURCE_KEY
        );

        return $this->handleView($this->view($listRepresentation));
    }
}
