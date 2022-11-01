<?php

declare(strict_types=1);

namespace Jschaedl\MobilePay\AppPayment;

use Jschaedl\Api\Exception\ClientException;
use Jschaedl\Api\Exception\ServerException;
use Jschaedl\Api\Exception\UnexpectedResponseException;
use Jschaedl\MobilePay\AppPayment\Exception\MobilePayClientException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jschaedl\MobilePay\AppPayment\ResponseHandler
 * @covers \Jschaedl\Api\Response
 * @covers \Jschaedl\Api\Exception\UnexpectedResponseException
 * @covers \Jschaedl\Api\Exception\ClientException
 * @covers \Jschaedl\Api\Exception\ServerException
 * @covers \Jschaedl\Api\Exception\ResponseException
 * @covers \Jschaedl\MobilePay\AppPayment\Exception\MobilePayClientException
 *
 * @uses \Jschaedl\Api\Validation\ValidationRule
 */
final class ResponseHandlerTest extends TestCase
{
    public function test_200(): void
    {
        $response = (new ResponseHandler())->handle(200, ['test' => 'test']);

        static::assertSame(200, $response->getStatusCode());
        static::assertSame(['test' => 'test'], $response->getBody());
    }

    public function test_204(): void
    {
        $response = (new ResponseHandler())->handle(204, []);

        static::assertSame(204, $response->getStatusCode());
        static::assertEmpty($response->getBody());
    }

    public function test_401(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('401 Unauthorized');

        (new ResponseHandler())->handle(401, []);
    }

    public function test_403(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('403 Forbidden');

        (new ResponseHandler())->handle(403, []);
    }

    public function test_404(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('404 Not Found');

        (new ResponseHandler())->handle(404, []);
    }

    public function test_409(): void
    {
        $this->expectException(MobilePayClientException::class);
        $this->expectExceptionMessage('409 code: invalid_payment_state; message: Cannot cancel payment that is already captured.; correlationId: 8d4243bc-aa83-43c3-a55d-5da221facd9b; origin: MPY');

        (new ResponseHandler())->handle(409, [
            'code' => 'invalid_payment_state',
            'message' => 'Cannot cancel payment that is already captured.',
            'correlationId' => '8d4243bc-aa83-43c3-a55d-5da221facd9b',
            'origin' => 'MPY',
        ]);
    }

    public function test_500(): void
    {
        $this->expectException(ServerException::class);
        $this->expectExceptionMessage('500 Server error');

        (new ResponseHandler())->handle(500, []);
    }

    public function test_unexpected(): void
    {
        $this->expectException(UnexpectedResponseException::class);
        $this->expectExceptionMessage('300 Unexpected response: failure: Something went wrong');

        (new ResponseHandler())->handle(300, [
            'failure' => 'Something went wrong',
        ]);
    }
}
