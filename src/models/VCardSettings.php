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
use Yii;

class VCardSettings extends Model
{
    public ?string $salt = null;
}
