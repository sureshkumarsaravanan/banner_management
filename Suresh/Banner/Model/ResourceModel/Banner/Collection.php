<?php
namespace Suresh\Banner\Model\ResourceModel\Banner;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Suresh\Banner\Model\Banner;
use Suresh\Banner\Model\ResourceModel\Banner as BannerResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'banner_id';
    protected $_model = Model::class;

    protected function _construct()
    {
        $this->_init(Banner::class, BannerResource::class);
    }
}
