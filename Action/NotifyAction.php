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
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use Heidelpay\PhpPaymentApi\Response as UnzerResponse;

/**
 * Class NotifyAction
 * @package CoreShop\Payum\Unzer\Action
 */
class NotifyAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;
    use ApiAwareTrait;

    public function __construct()
    {
        $this->apiClass = Api::class;
    }

    /**
     * {@inheritDoc}
     *
     * @param $request Notify
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $httpRequest = new GetHttpRequest();
        $this->gateway->execute($httpRequest);

        if ($httpRequest->method === 'POST') {
            $unzerResponse = new UnzerResponse($_POST);
            $shortId = $unzerResponse->getIdentification()->getShortId();
            $paymentReference = $unzerResponse->getPaymentReferenceId();

            $model['shortId'] = $shortId;
            $model['paymentReferenceId'] = $paymentReference;
            $model['isSuccess'] = $unzerResponse->isSuccess();
            $model['isError'] = $unzerResponse->isError();
            $model['isPending'] = $unzerResponse->isPending();
            $model['error'] = $unzerResponse->getError();

            throw new HttpResponse($httpRequest->query['afterUrl'], 200);
        }

        throw new HttpRedirect($httpRequest->query['afterUrl']);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Notify &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
