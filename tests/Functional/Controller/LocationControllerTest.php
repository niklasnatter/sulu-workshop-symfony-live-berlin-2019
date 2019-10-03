<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\Traits\LocationTrait;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Component\HttpFoundation\Response;

class LocationControllerTest extends SuluTestCase
{
    use LocationTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->purgeDatabase();
    }

    public function testCGet(): void
    {
        $client = $this->createAuthenticatedClient();

        $location1 = $this->createLocation('Sulu');
        $location2 = $this->createLocation('Symfony');

        $client->request('GET', '/admin/api/locations');

        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent(), true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertSame(2, $result['total']);
        $this->assertCount(2, $result['_embedded']['locations']);
        $items = $result['_embedded']['locations'];

        $this->assertSame($location1->getId(), $items[0]['id']);
        $this->assertSame($location2->getId(), $items[1]['id']);

        $this->assertSame($location1->getName(), $items[0]['name']);
        $this->assertSame($location2->getName(), $items[1]['name']);
    }
}
