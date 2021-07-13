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

use CoreShop\Payum\Unzer\Action\ObtainTokenAction;
use CoreShop\Payum\Unzer\Action\CaptureAction;
use CoreShop\Payum\Unzer\Action\ConvertPaymentAction;
use CoreShop\Payum\Unzer\Action\NotifyAction;
use CoreShop\Payum\Unzer\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class UnzerGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'unzer',
            'payum.factory_title' => 'Unzer',
            'payum.action.capture' => new CaptureAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.template.obtain_token_path' => '@PayumUnzer/Action',
            'payum.action.api.unzer_obtain_token' => function (ArrayObject $config) {
                return new ObtainTokenAction($config['payum.template.obtain_token_path']);
            },
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = [
                'sandboxMode' => true,
            ];
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [
                'paymentType',
                'privateKey',
                'publicKey',
            ];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new Api($config['privateKey'], $config['publicKey'], $config['paymentType']);
            };

            $config['payum.paths'] = array_replace([
                'PayumUnzer' => __DIR__.'/Resources/views',
            ], $config['payum.paths'] ?: []);
        }
    }
}
