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

    public function testGet(): void
    {
        $client = $this->createAuthenticatedClient();

        $location = $this->createLocation('Sulu');

        $client->request('GET', '/admin/api/locations/' . $location->getId());

        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent(), true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertSame($location->getId(), $result['id']);
        $this->assertSame($location->getName(), $result['name']);
    }

    public function testPost(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request(
            'POST',
            '/admin/api/locations',
            [
                'name' => 'Sulu',
                'street' => 'Teststreet',
                'number' => '42',
                'postalCode' => '6850',
                'city' => 'Dornbirn',
                'countryCode' => 'AT',
            ]
        );

        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent(), true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertArrayHasKey('id', $result);
        $this->assertNotNull($result['id']);
        $this->assertSame('Sulu', $result['name']);
        $this->assertSame('Teststreet', $result['street']);
        $this->assertSame('42', $result['number']);
        $this->assertSame('6850', $result['postalCode']);
        $this->assertSame('Dornbirn', $result['city']);
        $this->assertSame('AT', $result['countryCode']);

        $result = $this->findLocationById($result['id']);

        $this->assertNotNull($result);
        $this->assertSame('Sulu', $result->getName());
        $this->assertSame('Teststreet', $result->getStreet());
        $this->assertSame('42', $result->getNumber());
        $this->assertSame('6850', $result->getPostalCode());
        $this->assertSame('Dornbirn', $result->getCity());
        $this->assertSame('AT', $result->getCountryCode());
    }

    public function testPostNullValues(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request(
            'POST',
            '/admin/api/locations',
            [
                'name' => 'Sulu',
            ]
        );

        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent(), true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertArrayHasKey('id', $result);
        $this->assertNotNull($result['id']);
        $this->assertSame('Sulu', $result['name']);
        $this->assertEmpty($result['street']);
        $this->assertEmpty($result['number']);
        $this->assertEmpty($result['postalCode']);
        $this->assertEmpty($result['city']);
        $this->assertEmpty($result['countryCode']);

        $result = $this->findLocationById($result['id']);

        $this->assertNotNull($result);
        $this->assertSame('Sulu', $result->getName());
        $this->assertEmpty($result->getStreet());
        $this->assertEmpty($result->getNumber());
        $this->assertEmpty($result->getPostalCode());
        $this->assertEmpty($result->getCity());
        $this->assertEmpty($result->getCountryCode());
    }

    public function testPut(): void
    {
        $client = $this->createAuthenticatedClient();

        $location = $this->createLocation('Symfony');

        $client->request(
            'PUT',
            '/admin/api/locations/' . $location->getId(),
            [
                'name' => 'Sulu',
                'street' => 'Teststreet',
                'number' => '42',
                'postalCode' => '6850',
                'city' => 'Dornbirn',
                'countryCode' => 'AT',
            ]
        );

        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent(), true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertArrayHasKey('id', $result);
        $this->assertNotNull($result['id']);
        $this->assertSame('Sulu', $result['name']);
        $this->assertSame('Teststreet', $result['street']);
        $this->assertSame('42', $result['number']);
        $this->assertSame('6850', $result['postalCode']);
        $this->assertSame('Dornbirn', $result['city']);
        $this->assertSame('AT', $result['countryCode']);

        $result = $this->findLocationById($result['id']);

        $this->assertNotNull($result);
        $this->assertSame('Sulu', $result->getName());
        $this->assertSame('Teststreet', $result->getStreet());
        $this->assertSame('42', $result->getNumber());
        $this->assertSame('6850', $result->getPostalCode());
        $this->assertSame('Dornbirn', $result->getCity());
        $this->assertSame('AT', $result->getCountryCode());
    }

    public function testPutNullValues(): void
    {
        $client = $this->createAuthenticatedClient();

        $location = $this->createLocation('Symfony');

        $client->request(
            'PUT',
            '/admin/api/locations/' . $location->getId(),
            [
                'name' => 'Sulu',
            ]
        );

        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent(), true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertArrayHasKey('id', $result);
        $this->assertNotNull($result['id']);
        $this->assertSame('Sulu', $result['name']);
        $this->assertEmpty($result['street']);
        $this->assertEmpty($result['number']);
        $this->assertEmpty($result['postalCode']);
        $this->assertEmpty($result['city']);
        $this->assertEmpty($result['countryCode']);

        $result = $this->findLocationById($result['id']);

        $this->assertNotNull($result);
        $this->assertSame('Sulu', $result->getName());
        $this->assertEmpty($result->getStreet());
        $this->assertEmpty($result->getNumber());
        $this->assertEmpty($result->getPostalCode());
        $this->assertEmpty($result->getCity());
        $this->assertEmpty($result->getCountryCode());
    }

    public function testDelete(): void
    {
        $client = $this->createAuthenticatedClient();

        $location = $this->createLocation('Symfony');

        /** @var int $locationId */
        $locationId = $location->getId();

        $client->request('DELETE', '/admin/api/locations/' . $location->getId());

        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertHttpStatusCode(204, $response);

        $this->assertNull($this->findLocationById($locationId));
    }
}
