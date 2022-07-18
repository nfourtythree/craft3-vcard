<?php
/**
 * vCard plugin for Craft CMS 4.x
 *
 * vCard generator plugin for Craft CMS 4
 *
 * @link      http://n43.me
 * @copyright Copyright (c) 2022 Nathaniel Hammond (nfourtythree)
 */

namespace nfourtythree\vcard\controllers;

use Craft;

use craft\web\Controller;
use nfourtythree\vcard\VCard;
use yii\base\ExitException;

/**
 * Default Controller
 *
 * @author    Nathaniel Hammond (nfourtythree)
 * @package   VCard
 * @since     1.0.0
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    protected int|bool|array $allowAnonymous = ['index'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/vcard/default
     *
     * @param $vcard
     * @throws ExitException
     */
    public function actionIndex($vcard): void
    {
        $options = VCard::getInstance()->getService()->decodeUrlParam($vcard);

        VCard::getInstance()->getService()->generateVcard($options);

        Craft::$app->end();
    }
}
