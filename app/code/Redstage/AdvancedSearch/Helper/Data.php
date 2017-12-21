<?php

namespace Redstage\AdvancedSearch\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

    const XML_PATH_ENABLED = 'redstage_advancedsearch/general/';

    public function isEnabled(){
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLED,ScopeInterface::SCOPE_STORE);
    }
}