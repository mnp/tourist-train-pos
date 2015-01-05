<?php
// A small subset of Pear's I18N_Country, overriding/extending it.

require_once 'HTML/Select/Common/Country.php';

class MiniCountry extends HTML_Select_Common_Country
{
    function MiniCountry()
    {
        $this->_codes = array(
            'CA' => 'CANADA',
            'DE' => 'GERMANY',
            'FR' => 'FRANCE',
            'GB' => 'UNITED KINGDOM',
            'US' => 'UNITED STATES',
            'xx' => 'Other'
            );
    }
}
