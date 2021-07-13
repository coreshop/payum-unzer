<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Payum\Unzer;

use UnzerSDK\Unzer;

class Api
{
    protected Unzer $api;
    protected string $paymentType;
    protected string $publicKey;

    public function __construct(string $privateKey, string $publickKey, string $paymentType)
    {
        $this->api = new Unzer($privateKey);
        $this->paymentType = $paymentType;
        $this->publicKey = $publickKey;
    }

    public function getPaymentType(): string
    {
        return $this->paymentType;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getApi(): Unzer
    {
        return $this->api;
    }
}
