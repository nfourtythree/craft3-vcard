<?php

namespace nfourtythree\vcard\variables;

use nfourtythree\vcard\VCard;

use Craft;
use craft\helpers\Template;

class VCardVariable
{
  // Protected Static Properties
  // =========================================================================


  // Public Methods
  // =========================================================================

  /**
   * Constructor.
   */
  public function __construct()
  {
  }

  public function link( $options = [] )
  {
    return VCard::$plugin->service->generateLink( $options );
  }

  public function output( $options = [] )
  {
    return VCard::$plugin->service->generateOutput( $options );
  }

}
