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

namespace CoreShop\Payum\Unzer\Action\Api;

use CoreShop\Payum\Unzer\Api;
use CoreShop\Payum\Unzer\Request\Api\ObtainToken;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\RenderTemplate;

/**
 * Class ObtainTokenAction
 * @package CoreShop\Payum\Unzer\Action\Api
 */
class ObtainTokenAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    /**
     * @var string
     */
    protected $templateName;

    /**
     * @param string $templateName
     */
    public function __construct($templateName)
    {
        $this->apiClass = Api::class;
        $this->templateName = $templateName;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request ObtainToken */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $api = $this->api->getApi();

        if ($api->getResponse()->isSuccess()) {
            $this->gateway->execute($renderTemplate = new RenderTemplate($this->templateName, array(
                'model' => $model,
                'frame_src' => $api->getResponse()->getPaymentFormUrl(),
            )));

            throw new HttpResponse($renderTemplate->getResult());
        }

        throw new \Exception($api->getResponse()->getError()['message'], $api->getResponse()->getError()['code']);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof ObtainToken &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
