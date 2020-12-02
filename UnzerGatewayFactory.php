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

use CoreShop\Payum\Unzer\Action\Api\UnzerCreditCardCaptureAction;
use CoreShop\Payum\Unzer\Action\Api\UnzerDebitCardCaptureAction;
use CoreShop\Payum\Unzer\Action\Api\UnzerPayPalCaptureAction;
use CoreShop\Payum\Unzer\Action\Api\UnzerSofortCaptureAction;
use CoreShop\Payum\Unzer\Action\Api\ObtainTokenAction;
use CoreShop\Payum\Unzer\Action\Api\PopulateUnzerAction;
use CoreShop\Payum\Unzer\Action\CaptureAction;
use CoreShop\Payum\Unzer\Action\ConvertPaymentAction;
use CoreShop\Payum\Unzer\Action\NotifyAction;
use CoreShop\Payum\Unzer\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

/**
 * Class UnzerGatewayFactory
 * @package CoreShop\Payum\Unzer
 */
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
            'payum.action.notify' => new NotifyAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.api.populate_unzer' => new PopulateUnzerAction(),
            'payum.action.api.unzer_paypal_capture' => new UnzerPayPalCaptureAction(),
            'payum.action.api.unzer_sofort_capture' => new UnzerSofortCaptureAction(),
            'payum.action.api.unzer_creditcard_capture' => new UnzerCreditCardCaptureAction(),
            'payum.action.api.unzer_debitcard_capture' => new UnzerDebitCardCaptureAction(),
            'payum.template.obtain_token' => '@PayumUnzer/Action/obtain_checkout_token.html.twig',
            'payum.action.api.unzer_obtain_token' => function (ArrayObject $config) {
                return new ObtainTokenAction($config['payum.template.obtain_token']);
            },
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = [
                'sandboxMode' => true
            ];
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [
                'gatewayType',
                'securitySender',
                'userLogin',
                'userPassword',
                'transactionChannel'
            ];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new Api(
                    $config['gatewayType'],
                    [
                        'securitySender' => $config['securitySender'],
                        'userLogin' => $config['userLogin'],
                        'userPassword' => $config['userPassword'],
                        'transactionChannel' => $config['transactionChannel'],
                        'sandboxMode' => $config['sandboxMode'],
                    ],
                    $config['payum.http_client'],
                    $config['httplug.message_factory']
                );
            };

            $config['payum.paths'] = array_replace([
            'PayumUnzer' => __DIR__.'/Resources/views',
        ], $config['payum.paths'] ?: []);
        }
    }
}
