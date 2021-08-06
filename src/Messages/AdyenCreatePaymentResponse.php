<?php

declare(strict_types=1);

/**
 * Contains the AdyenCreatePaymentResponse class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-08-03
 *
 */

namespace Vanilo\Adyen\Messages;

use Illuminate\Contracts\Support\Responsable;

final class AdyenCreatePaymentResponse implements Responsable
{
    public string $resultCode;

    public ?array $action;

    public function __construct(string $resultCode, ?array $action = null)
    {
        $this->resultCode = $resultCode;
        $this->action = $action;
    }

    public function toResponse($request)
    {
        $response = [
            'resultCode' => $this->resultCode,
        ];

        if (null !== $this->action) {
            $request['action'] = $this->action;
        }

        return $response;
    }
}
