<?php

namespace Suresh\Banner\Model;

use Magento\Framework\Model\AbstractModel;
use Suresh\Banner\Model\ResourceModel\Banner AS BannerResource;

class Banner extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(BannerResource::class, );
    }
}