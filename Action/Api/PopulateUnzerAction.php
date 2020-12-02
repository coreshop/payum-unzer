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

use CoreShop\Payum\Unzer\Request\Api\PopulateUnzer;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;

/**
 * Class PopulateUnzerAction
 * @package CoreShop\Payum\Unzer\Action\Api
 */
class PopulateUnzerAction implements ActionInterface
{
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel()->getModel());

        if (!$request instanceof PopulateUnzer) {
            return;
        }

        if ($details['customer']) {
            $customer = ArrayObject::ensureArrayObject($details['customer']);

            $request->getUnzerRequest()->customerAddress(
                $customer['name'],
                $customer['lastname'],
                $customer['company'],
                $customer['customer_id'],
                $customer['street'],
                $customer['state'],
                $customer['post_code'],
                $customer['city'],
                $customer['country_code'],
                $customer['email']
            );
        }
    }

    public function supports($request)
    {
        return $request instanceof PopulateUnzer;
    }
}
