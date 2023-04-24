<?php

namespace App\Tests\Controller\Backoffice;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RoleUserAccessTest extends WebTestCase
{
    /**
     * Routes en GET pour ROLE_USER
     * 
     * @dataProvider getUrls
     */
    public function testPageGetIsForbidden($url)
    {
        // On crée un client
        $client = static::createClient();

        // Le Repo des Users
        $userRepository = static::getContainer()->get(UserRepository::class);
        // On récupère user@user.com
        $testUser = $userRepository->findOneByEmail('user1@user.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        // On exécute la requête APRES être loggué
        $client->request('GET', $url);

        // Le ROLE_USER aura un status code 403
        $this->assertResponseStatusCodeSame(403);
    }

    public function getUrls()
    {
        yield ['/back/genre/1'];
        yield ['/back/category/'];
        yield ['/back/category/new'];
        yield ['/back/category/1/edit'];
        yield ['/back/user/'];
        yield ['/back/user/new'];
        yield ['/back/user/1'];
        yield ['/back/user/1/edit'];
        // ...
    }

    /**
     * Routes en POST pour ROLE_USER
     * 
     * @dataProvider postUrls
     */
    public function testPagePostIsForbidden($url)
    {
        // On crée un client
        $client = static::createClient();

        // Le Repo des Users
        $userRepository = static::getContainer()->get(UserRepository::class);
        // On récupère user@user.com
        $testUser = $userRepository->findOneByEmail('user1@user.com');
        // simulate $testUser being logged in
        $client->loginUser($testUser);

        // On exécute la requête APRES être loggué
        $client->request('POST', $url);

        // Le ROLE_USER aura un status code 403
        $this->assertResponseStatusCodeSame(403);
    }

    public function postUrls()
    {
        yield ['/back/genre/new'];
        yield ['/back/category/1/edit'];
        yield ['/back/user/new'];
        yield ['/back/user/1/edit'];
        // ...
    }

}