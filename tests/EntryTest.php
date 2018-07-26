<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class EntryTest extends WebTestCase
{
    /**
     * @test
     * @dataProvider urlProvider
    */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        yield ['/entry'];
        yield ['/entry/new'];
        yield ['/entry/1'];
        yield ['/entry/1/edit'];  
    }

    /** @test */
    public function first_number_has_to_be_numeric()
    {
        $client = static::createClient();
        $client->request('POST', 'entry/new', [
            'a' => 'A',
            'b' => 2
        ]);

        $this->assertSame(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $client->getResponse()->getStatusCode()
        );
    }

    /** @test */
    public function second_number_has_to_be_numeric()
    {
        $client = static::createClient();
        $client->request('POST', 'entry/new', [
            'a' => 1,
            'b' => 'B'
        ]);

        $this->assertSame(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $client->getResponse()->getStatusCode()
        );
    }

 
   

  
   
}
