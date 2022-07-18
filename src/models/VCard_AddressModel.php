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
 * VCard_AddressModel Model
 *
 * @author    Nathaniel Hammond (nfourtythree)
 * @package   VCard
 * @since     1.0.0
 */
class VCard_AddressModel extends Model
{
    public string $name = '';
    public string $extended = '';
    public string $street = '';
    public string $city = '';
    public string $region = '';
    public string $zip = '';
    public string $country = '';
    public string $type = '';

    /**
     * @inerhitdoc
     */
    public function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = ['name', 'string'];
        $rules[] = ['extended', 'string'];
        $rules[] = ['street', 'string'];
        $rules[] = ['city', 'string'];
        $rules[] = ['region', 'string'];
        $rules[] = ['zip', 'string'];
        $rules[] = ['country', 'string'];
        $rules[] = ['type', 'string'];

        return $rules;
    }
}
