<?php
/**
 * vCard plugin for Craft CMS 4.x
 *
 * vCard generator plugin for Craft CMS 4
 *
 * @link      http://n43.me
 * @copyright Copyright (c) 2022 Nathaniel Hammond (nfourtythree)
 */

namespace nfourtythree\vcard\variables;

use nfourtythree\vcard\VCard;

use yii\base\ExitException;
use yii\web\HttpException;
use yii\web\RangeNotSatisfiableHttpException;

class VCardVariable
{
    /**
     * @param array $options
     * @return string|null
     */
    public function link(array $options = []): ?string
    {
        return VCard::getInstance()->service->generateLink($options);
    }

    /**
     * @param array $options
     * @return string|void
     * @throws ExitException
     * @throws HttpException
     * @throws RangeNotSatisfiableHttpException
     */
    public function output(array $options = [])
    {
        return VCard::getInstance()->service->generateOutput($options);
    }
}
