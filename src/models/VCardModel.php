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
 * VCardModel Model
 *
 * @author    Nathaniel Hammond (nfourtythree)
 * @package   VCard
 * @since     1.0.0
 */
class VCardModel extends Model
{
    public string $firstName = '';
    public string $lastName = '';
    public string $additional = '';
    public string $prefix = '';
    public string $suffix = '';
    public string $company = '';
    public string $jobTitle = '';
    public array $email = [];
    public array $url = [];
    public array $address = [];
    public array $phoneNumber = [];
    public string $birthday = '';
    public string $note = '';
    public string $photo = '';

    /**
     * @inheritdoc
     */
    public function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = ['firstName', 'required'];
        $rules[] = [['firstName', 'lastName', 'additional', 'prefix', 'suffix', 'company', 'jobTitle', 'birthday', 'note', 'photo'], 'string'];
        return $rules;
    }
}
