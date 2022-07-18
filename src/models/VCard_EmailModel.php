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

use nfourtythree\vcard\VCard;

use Craft;
use craft\base\Model;

/**
 * VCard_EmailModel Model
 *
 * @author    Nathaniel Hammond (nfourtythree)
 * @package   VCard
 * @since     1.0.0
 */
class VCard_EmailModel extends Model
{
    public string $address = '';
    public string $type = '';

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = ['address', 'string'];
        $rules[] = ['type', 'string'];

        return $rules;
    }
}
