<?php
/**
 * vCard plugin for Craft CMS 3.x
 *
 * vCard generator plugin for Craft cms 3
 *
 * @link      http://n43.me
 * @copyright Copyright (c) 2019 Nathaniel Hammond (nfourtythree)
 */

namespace nfourtythree\vcard\services;

use nfourtythree\vcard\VCard;
use nfourtythree\vcard\models\VCardModel;
use nfourtythree\vcard\models\VCard_AddressModel;
use nfourtythree\vcard\models\VCard_EmailModel;
use nfourtythree\vcard\models\VCard_PhoneNumberModel;
use nfourtythree\vcard\models\VCard_UrlModel;

use JeroenDesloovere\VCard\VCard as VCardLib;

use Craft;
use craft\base\Component;
use craft\helpers\UrlHelper;
use RuntimeException;

/**
 * VCardService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Nathaniel Hammond (nfourtythree)
 * @package   VCard
 * @since     1.0.0
 */
class VCardService extends Component
{
    /**
     * @param array $options
     * @return string|null
     */
    public function generateLink(array $options = []): ?string
    {
        if ($this->_validateOptions($options)) {
            $encodedOptions = $this->encodeUrlParam($options);
            return UrlHelper::actionUrl('vcard/default/index', ['vcard' => $encodedOptions]);
        }

        return null;
    }

    /**
     * @param array $options
     * @return string|void
     */
    public function generateOutput(array $options = [])
    {
        return $this->generateVcard($options, "output");
    }

    /**
     * @param array $options
     * @param string $action
     * @return string|void
     */
    public function generateVcard(array $options = [], string $action = "download")
    {
        if ($this->_validateOptions($options)) {

            if (isset($options['address'])) {
                $options['address'] = $this->_populateAddressModel($options['address']);
            }

            if (isset($options['phoneNumber'])) {
                $options['phoneNumber'] = $this->_populatePhoneNumberModel($options['phoneNumber']);
            }

            if (isset($options['email'])) {
                $options['email'] = $this->_populateEmailModel($options['email']);
            }

            if (isset($options['url'])) {
                $options['url'] = $this->_populateUrlModel($options['url']);
            }

            $vcard = new VCardModel($options);

            if ($vcard->validate()) {

                $vcardData = $this->_createVcardData($vcard);

                switch ($action) {
                    case 'output':
                        return $vcardData->getOutput();
                    case 'download':
                    default:
                        $vcardData->download();
                        break;
                }
            } else {

                foreach ($vcard->getErrors() as $error) {
                    Craft::error($error, __METHOD__);
                }
            }
        }
    }

    /**
     * @param $options
     * @return bool
     */
    private function _validateOptions($options): bool
    {
        if (empty($options)) {
            Craft::error(Craft::t('vcard', 'vCard Parameters must be supplied'), __METHOD__);
        }

        return true;
    }

    /**
     * @param array $address
     * @return array|VCard_AddressModel[]|string
     */
    private function _populateAddressModel(array $address)
    {
        if (!empty($address)) {
            // check to see if we are dealing with multiple addresses
            $tmp = [];
            foreach ($address as $a) {
                $tmp[] = new VCard_AddressModel($a);
            }
            return $tmp;
        }

        return '';
    }

    /**
     * @param $phoneNumber
     * @return array|VCard_PhoneNumberModel[]|string
     */
    private function _populatePhoneNumberModel($phoneNumber)
    {
        if (is_array($phoneNumber) or (is_string($phoneNumber) and $phoneNumber)) {

            $phoneNumber = $this->_createArrayFromString("number", $phoneNumber);
            // check to see if we are dealing with multiple phoneNumbers
            if (count($phoneNumber) == count($phoneNumber, COUNT_RECURSIVE)) {
                return [new VCard_PhoneNumberModel($phoneNumber)];
            }

            $tmp = [];
            foreach ($phoneNumber as $row) {
                $tmp[] = new VCard_PhoneNumberModel($this->_createArrayFromString("number", $row));
            }

            return $tmp;
        }

        return '';
    }

    /**
     * @param $key
     * @param $value
     * @return string[]
     */
    private function _createArrayFromString($key, $value): array
    {
        if (is_string($value)) {
            return [$key => $value];
        }
        // only switch if it is a string
        return $value;
    }

    /**
     * @param $email
     * @return array|VCard_EmailModel[]|string
     */
    private function _populateEmailModel($email)
    {
        if (is_array($email) or (is_string($email) and $email)) {

            $email = $this->_createArrayFromString("address", $email);
            // check to see if we are dealing with multiple emails
            if (count($email) == count($email, COUNT_RECURSIVE)) {
                return [new VCard_EmailModel($email)];
            }

            $tmp = [];
            foreach ($email as $row) {
                $tmp[] = new VCard_EmailModel($this->_createArrayFromString("address", $row));
            }
            return $tmp;
        }

        return '';
    }

    /**
     * @param $url
     * @return array|VCard_UrlModel[]|string
     */
    private function _populateUrlModel($url)
    {
        if (is_array($url) or (is_string($url) and $url)) {

            $url = $this->_createArrayFromString("address", $url);
            // check to see if we are dealing with multiple emails
            if (count($url) == count($url, COUNT_RECURSIVE)) {
                return [new VCard_UrlModel($url)];
            }

            $tmp = [];
            foreach ($url as $row) {
                $tmp[] = new VCard_EmailModel($this->_createArrayFromString("address", $row));
            }
            return $tmp;
        }

        return '';
    }

    /**
     * @param VCardModel $vcardModel
     * @return VCardLib
     */
    private function _createVcardData(VCardModel $vcardModel): VCardLib
    {
        $vcard = new VCardLib();

        $vcard->addName($vcardModel->lastName, $vcardModel->firstName, $vcardModel->additional, $vcardModel->prefix, $vcardModel->suffix);

        if ($vcardModel->company) {
            $vcard->addCompany($vcardModel->company);
        }

        if ($vcardModel->jobTitle) {
            $vcard->addJobtitle($vcardModel->jobTitle);
        }

        if ($vcardModel->url and is_array($vcardModel->url)) {
            foreach ($vcardModel->url as $url) {
                if (($url instanceof VCard_UrlModel) && $url->validate()) {
                    $vcard->addUrl(
                        $url->address,
                        $url->type
                    );
                }
            }
        }

        if ($vcardModel->address and is_array($vcardModel->address)) {
            foreach ($vcardModel->address as $address) {
                if (($address instanceof VCard_AddressModel) && $address->validate()) {
                    $vcard->addAddress(
                        $address->name,
                        $address->extended,
                        $address->street,
                        $address->city,
                        $address->region,
                        $address->zip,
                        $address->country,
                        $address->type
                    );
                }
            }
        }

        if ($vcardModel->phoneNumber and is_array($vcardModel->phoneNumber)) {
            foreach ($vcardModel->phoneNumber as $phoneNumber) {
                if (($phoneNumber instanceof VCard_PhoneNumberModel) && $phoneNumber->validate()) {
                    $vcard->addPhoneNumber(
                        $phoneNumber->number,
                        $phoneNumber->type
                    );
                }
            }
        }

        if ($vcardModel->email and is_array($vcardModel->email)) {
            foreach ($vcardModel->email as $email) {
                if (($email instanceof VCard_EmailModel) && $email->validate()) {
                    $vcard->addEmail(
                        $email->address,
                        $email->type
                    );
                }
            }
        }

        if ($vcardModel->photo) {
            $vcard->addPhoto($vcardModel->photo);
        }

        if ($vcardModel->note) {
            $vcard->addNote($vcardModel->note);
        }

        return $vcard;
    }

    /**
     * @param array $options
     * @return string
     */
    public function encodeUrlParam(array $options = []): string
    {
        $optionsString = serialize($options);

        return $this->encrypt($optionsString);
    }

    /**
     * @param string $optionsString
     * @return mixed
     */
    public function decodeUrlParam(string $optionsString = "")
    {
        $optionsString = $this->decrypt($optionsString);
        return unserialize($optionsString);
    }

    /**
     * @param $string
     * @return string
     */
    protected function encrypt($string): string
    {
        $key = VCard::$plugin->getSettings()->salt;
        if (!$key || $key == 's34s4L7') {
            throw new RuntimeException('You must provide a valid salt key.');
        }

        $key = md5($key);
        $iv = substr(md5($key), 0, 16);

        return rtrim(
            strtr(
                base64_encode(
                    openssl_encrypt($string, 'aes128', md5($key), true, $iv)
                ),
                '+/', '-_'
            ), '='
        );
    }

    /**
     * @param $string
     * @return string
     */
    public function decrypt($string): string
    {
        $key = VCard::$plugin->getSettings()->salt;
        if (!$key || $key == 's34s4L7') {
            throw new RuntimeException('You must provide a valid salt key.');
        }

        $key = md5($key);
        $iv = substr(md5($key), 0, 16);

        return rtrim(
            openssl_decrypt(
                base64_decode(str_pad(strtr($string, '-_', '+/'), strlen($string) % 4, '=', STR_PAD_RIGHT)),
                'aes128',
                md5($key),
                true,
                $iv
            ),
            "\0"
        );
    }

}
