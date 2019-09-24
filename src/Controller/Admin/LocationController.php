<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\DoctrineListRepresentationFactory;
use App\Entity\Location;
use App\Repository\LocationRepository;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Sulu\Component\Rest\RestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LocationController extends RestController implements ClassResourceInterface
{
    /**
     * @var LocationRepository
     */
    private $repository;

    /**
     * @var DoctrineListRepresentationFactory
     */
    private $doctrineListRepresentationFactory;

    public function __construct(
        LocationRepository $repository,
        DoctrineListRepresentationFactory $doctrineListRepresentationFactory
    ) {
        $this->doctrineListRepresentationFactory = $doctrineListRepresentationFactory;
        $this->repository = $repository;
    }

    public function cgetAction(): Response
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            Location::RESOURCE_KEY
        );

        return $this->handleView($this->view($listRepresentation));
    }

    public function getAction(int $id, Request $request): Response
    {
        $entity = $this->load($id);
        if (!$entity) {
            throw new NotFoundHttpException();
        }

        return $this->handleView($this->view($entity));
    }

    public function postAction(Request $request): Response
    {
        $entity = $this->create();

        $this->mapDataToEntity($request->request->all(), $entity);

        $this->save($entity);

        return $this->handleView($this->view($entity));
    }

    public function putAction(int $id, Request $request): Response
    {
        $entity = $this->load($id);
        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $this->mapDataToEntity($request->request->all(), $entity);

        $this->save($entity);

        return $this->handleView($this->view($entity));
    }

    public function deleteAction(int $id): Response
    {
        $this->remove($id);

        return $this->handleView($this->view());
    }

    /**
     * @param string[] $data
     */
    protected function mapDataToEntity(array $data, Location $entity): void
    {
        $entity->setName($data['name']);
        $entity->setStreet($data['street'] ?? '');
        $entity->setNumber($data['number'] ?? '');
        $entity->setCity($data['city'] ?? '');
        $entity->setPostalCode($data['postalCode'] ?? '');
        $entity->setCountryCode($data['countryCode'] ?? '');
    }

    protected function load(int $id): ?Location
    {
        return $this->repository->findById($id);
    }

    protected function create(): Location
    {
        return $this->repository->create();
    }

    protected function save(Location $entity): void
    {
        $this->repository->save($entity);
    }

    protected function remove(int $id): void
    {
        $this->repository->remove($id);
    }
}
