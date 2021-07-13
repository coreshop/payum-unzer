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

namespace CoreShop\Payum\Unzer\Action;

use CoreShop\Payum\Unzer\Api;
use CoreShop\Payum\Unzer\Request\Api\ObtainToken;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHttpRequest;
use UnzerSDK\Unzer;

class CaptureAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;
    use ApiAwareTrait;

    public function __construct()
    {
        $this->apiClass = Api::class;
    }

    /**
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        /**
         * @var Unzer $unzerApi
         */
        $unzerApi = $this->api->getApi();

        if ($model['UNZER_PAYMENT_ID']) {
            $payment = $unzerApi->fetchPayment($model['UNZER_PAYMENT_ID']);
            $transaction = $payment->getInitialTransaction();

            if ($payment->isCompleted()) {
                $model['UNZER_PAYMENT_SUCCESS'] = 1;
            } elseif ($payment->isPending()) {
                if ($transaction && $transaction->isSuccess()) {
                    $model['UNZER_PAYMENT_SUCCESS'] = 1;
                }
                elseif ($transaction && $transaction->isPending()) {
                    $model['UNZER_PAYMENT_PENDING'] = 1;
                }
            }
            elseif ($transaction) {
                $model['UNZER_PAYMENT_ERROR'] = $transaction->getMessage()->getCustomer();
                $model['UNZER_PAYMENT_MERCHANT_ERROR'] = $transaction->getMessage()->getMerchant();
            }

            return;
        }

        $this->gateway->execute($httpRequest = new GetHttpRequest());

        $postParams = [];
        parse_str($httpRequest->content, $postParams);

        if (isset($postParams['UNZER_RESOURCE_ID'])) {
            $model['UNZER_RESOURCE_ID'] = $postParams['UNZER_RESOURCE_ID'];

            $transaction = $unzerApi->charge(
                $model['amount'] / 100,
                $model['currency'],
                $model['UNZER_RESOURCE_ID'],
                $request->getToken()->getTargetUrl(),
                $model['UNZER_CUSTOMER'] ?? null,
                $model['number'],
                $model['UNZER_METADATA'] ?? null,
                $model['UNZER_BASKET'] ?? null,
                null,
                null,
                $model['description']
            );

            if ($transaction->isError()) {
                $model['UNZER_PAYMENT_ERROR'] = $transaction->getMessage()->getCustomer();
                $model['UNZER_PAYMENT_MERCHANT_ERROR'] = $transaction->getMessage()->getMerchant();

                throw new \Exception($transaction->getMessage()->getCustomer());
            }

            $model['UNZER_PAYMENT_ID'] = $transaction->getPaymentId();
            $model['UNZER_SHORT_PAYMENT_ID'] = $transaction->getShortId();

            if ($transaction->getRedirectUrl()) {
                throw new HttpRedirect($transaction->getRedirectUrl());
            }

            return;
        }

        $obtainToken = new ObtainToken($request->getToken());
        $obtainToken->setModel($model);

        $this->gateway->execute($obtainToken);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
