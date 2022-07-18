<?php
/**
 * vCard plugin for Craft CMS 4.x
 *
 * vCard generator plugin for Craft CMS 4
 *
 * @link      http://n43.me
 * @copyright Copyright (c) 2022 Nathaniel Hammond (nfourtythree)
 */

namespace nfourtythree\vcard\models;

use craft\base\Model;

use nfourtythree\vcard\VCard;

/**
 * VCard_PhoneNumberModel Model
 *
 * @author    Nathaniel Hammond (nfourtythree)
 * @package   VCard
 * @since     1.0.0
 */
class VCard_PhoneNumberModel extends Model
{
    public string $number = '';
    public string $type = '';

    /**
     * @inheritdoc
     */
    public function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = ['number', 'string'];
        $rules[] = ['type', 'string'];

        return $rules;
    }
}
