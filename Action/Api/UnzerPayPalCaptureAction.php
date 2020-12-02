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

use CoreShop\Payum\Unzer\Request\Api\UnzerCapture;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;

/**
 * Class UnzerPayPalCaptureAction
 * @package CoreShop\Payum\Unzer\Action\Api
 */
class UnzerPayPalCaptureAction implements ActionInterface
{
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        if (!$request instanceof UnzerCapture) {
            return;
        }

        $request->getApi()->getApi()->debit();
    }

    public function supports($request)
    {
        return $request instanceof UnzerCapture &&
                $request->getApi()->getType() === 'PayPal';
    }
}
