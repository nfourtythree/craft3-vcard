<?php
/**
 * vCard plugin for Craft CMS 3.x
 *
 * vCard generator plugin for Craft cms 3
 *
 * @link      http://n43.me
 * @copyright Copyright (c) 2019 Nathaniel Hammond (nfourtythree)
 */

namespace nfourtythree\vcard\models;

use nfourtythree\vcard\VCard;

use Craft;
use craft\base\Model;

/**
 * VCardModel Model
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Nathaniel Hammond (nfourtythree)
 * @package   VCard
 * @since     1.0.0
 */
class VCardModel extends Model
{
    // Public Properties
    // =========================================================================

    public $firstName = '';
    public $lastName = '';
    public $additional = '';
    public $prefix = '';
    public $suffix = '';
    public $company = '';
    public $jobTitle = '';
    public $email = '';
    public $url = '';
    public $address = '';
    public $phoneNumber = '';
    public $birthday = '';
    public $note = '';
    public $photo = '';

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
          [ 'firstName', 'required' ],
          [ [ 'firstName', 'lastName', 'additional', 'prefix', 'suffix', 'company', 'jobTitle', 'birthday', 'note', 'photo' ], 'string' ]
        ];
    }
}
