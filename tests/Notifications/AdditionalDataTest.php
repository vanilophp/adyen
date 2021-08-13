<?php

declare(strict_types=1);

/**
 * Contains the AdditionalDataTest class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-08-13
 *
 */

namespace Vanilo\Adyen\Tests\Notifications;

use Vanilo\Adyen\Notifications\AdditionalData;
use Vanilo\Adyen\Tests\TestCase;

class AdditionalDataTest extends TestCase
{
    /** @test */
    public function it_reads_arbitrary_values()
    {
        $data = new AdditionalData(['asd' => 'qwe', '123' => 456]);

        $this->assertEquals('qwe', $data->get('asd'));
        $this->assertEquals(456, $data->get('123'));
    }

    /** @test */
    public function it_tells_if_the_hmac_signature_is_missing()
    {
        $data = new AdditionalData([]);

        $this->assertFalse($data->hasHmacSignature());
    }

    /** @test */
    public function it_tells_if_the_hmac_signature_is_present()
    {
        $data = new AdditionalData(['hmacSignature' => '123']);

        $this->assertTrue($data->hasHmacSignature());
    }

    /** @test */
    public function it_returns_the_hmac_signature()
    {
        $data = new AdditionalData(['hmacSignature' => 'oiruhfp2398jhfeuoiwfh']);

        $this->assertEquals('oiruhfp2398jhfeuoiwfh', $data->hmacSignature());
    }

    /** @test */
    public function it_returns_the_arn_if_present()
    {
        $data = new AdditionalData([]);
        $this->assertNull($data->arn());

        $data = new AdditionalData(['arn' => 'bretzel2000']);
        $this->assertEquals('bretzel2000', $data->arn());
    }

    /** @test */
    public function it_returns_the_acquirer_error_code_if_present()
    {
        $data = new AdditionalData([]);
        $this->assertNull($data->acquirerErrorCode());

        $data = new AdditionalData(['acquirerErrorCode' => 'too_many_attempts_oder_was']);
        $this->assertEquals('too_many_attempts_oder_was', $data->acquirerErrorCode());
    }

    /** @test */
    public function it_returns_the_acquirer_reference_if_present()
    {
        $data = new AdditionalData([]);
        $this->assertNull($data->acquirerReference());

        $data = new AdditionalData(['acquirerReference' => 'book_purchase_1984']);
        $this->assertEquals('book_purchase_1984', $data->acquirerReference());
    }

    /** @test */
    public function it_returns_the_alias_if_present()
    {
        $data = new AdditionalData([]);
        $this->assertNull($data->alias());

        $data = new AdditionalData(['alias' => '4****806']);
        $this->assertEquals('4****806', $data->alias());
    }

    /** @test */
    public function it_returns_the_alias_type_if_present()
    {
        $data = new AdditionalData([]);
        $this->assertNull($data->aliasType());

        $data = new AdditionalData(['aliasType' => 'card']);
        $this->assertEquals('card', $data->aliasType());
    }
}
