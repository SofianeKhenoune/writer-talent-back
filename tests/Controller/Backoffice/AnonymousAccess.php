<?php

namespace App\Tests\Controller\Backoffice;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AnonymousAccessTest extends WebTestCase
{
    /**
     * Routes en GET pour Anonymous
     * 
     * @dataProvider getUrls
     */
    public function testPageGetIsRedirect($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertResponseRedirects();
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
     * Routes en POST pour Anonymous
     * 
     * @dataProvider postUrls
     */
    public function testPagePostIsRedirect($url)
    {
        $client = self::createClient();
        $client->request('POST', $url);

        $this->assertResponseRedirects();
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