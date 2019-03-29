<?php

declare(strict_types=1);

namespace PhpCfdi\SatEstadoCfdi\Tests\Unit;

use PhpCfdi\SatEstadoCfdi\Consumer;
use PhpCfdi\SatEstadoCfdi\Tests\FakeConsumerClient;
use PhpCfdi\SatEstadoCfdi\Tests\TestCase;

class ConsumerTest extends TestCase
{
    public function testHasSameFactoryAndUriAsConstructed(): void
    {
        $client = new FakeConsumerClient();
        $uri = 'https://example.com';

        $consumer = new Consumer($client, $uri);
        $this->assertSame($client, $consumer->getClient());
        $this->assertSame($uri, $consumer->getUri());
    }

    public function testCallConsumerAndGetExpectedResponseStatus(): void
    {
        $fakeInput = [
            'CodigoEstatus' => 'S - Comprobante obtenido satisfactoriamente.',
            'Estado' => 'Vigente',
            'EsCancelable' => 'Cancelable con aceptación',
            'EstatusCancelacion' => 'En proceso',
        ];
        $fakeExpression = 'foo-bar';
        $fakeClient = new FakeConsumerClient($fakeInput);

        $consumer = new Consumer($fakeClient);
        $response = $consumer->execute($fakeExpression);

        $this->assertTrue($response->request()->isFound());
        $this->assertTrue($response->active()->isActive());
        $this->assertTrue($response->cancellable()->isCancellableByApproval());
        $this->assertTrue($response->cancellation()->isPending());
    }
}
