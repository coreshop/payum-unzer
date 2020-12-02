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

namespace CoreShop\Payum\Unzer\Request\Api;

use Payum\Core\Request\Generic;
use Heidelpay\PhpPaymentApi\Request as UnzerRequest;

/**
 * Class PopulateUnzer
 * @package CoreShop\Payum\Unzer\Request\Api
 */
class PopulateUnzer extends Generic
{
    /**
     * @var UnzerRequest
     */
    protected $unzerRequest;

    /**
     * @param $request
     * @param UnzerRequest $unzerRequest
     */
    public function __construct($request, UnzerRequest $unzerRequest)
    {
        parent::__construct($request);

        $this->unzerRequest = $unzerRequest;
    }

    /**
     * @return UnzerRequest
     */
    public function getUnzerRequest()
    {
        return $this->unzerRequest;
    }
}
