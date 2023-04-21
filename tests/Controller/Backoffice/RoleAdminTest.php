<?php

namespace App\Tests\Controller\Backoffice;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RoleAdminTest extends WebTestCase
{
    /**
     * Routes en GET pour ROLE_ADMIN
     * 
     * @dataProvider getUrls
     */
    public function testPageGetIsSuccessful($url)
    {
        // On crée un client
        $client = static::createClient();

        // Le Repo des Users
        $userRepository = static::getContainer()->get(UserRepository::class);
        // On récupère admin@admin.com
        $testUser = $userRepository->findOneByEmail('admin@admin.com');
        // simulate $testUser being logged in
        $client->loginUser($testUser);

        // On exécute la requête APRES être loggué
        $client->request('GET', $url);

        // Le ROLE_ADMIN aura un status code 200
        $this->assertResponseStatusCodeSame(200);
    }

    public function getUrls()
    {
        yield ['/back/user/'];
        yield ['/back/user/new'];
        yield ['/back/user/1'];
        yield ['/back/user/1/edit'];
        // ...
    }

    /**
     * Routes pour "new" pour ROLE_ADMIN
     * 
     * @dataProvider postUrlsNew
     */
    public function testPagePostIsUnprocessable($url)
    {
        // On crée un client
        $client = static::createClient();

        // Le Repo des Users
        $userRepository = static::getContainer()->get(UserRepository::class);
        // On récupère admin@admin.com
        $testUser = $userRepository->findOneByEmail('admin@admin.com');
        // simulate $testUser being logged in
        $client->loginUser($testUser);

        // On exécute la requête APRES être loggué
        $client->request('GET', $url);

        // On affiche le form
        $this->assertResponseStatusCodeSame(200);

        // On soumet le form vide
        $crawler = $client->submitForm('Enregistrer');

        // Le ROLE_ADMIN aura un status code 422
        $this->assertResponseStatusCodeSame(422);
        // ou $this->assertResponseIsUnprocessable();
    }

    public function postUrlsNew()
    {
        yield ['/back/genre/new'];
    }
}