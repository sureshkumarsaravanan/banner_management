<?php

namespace Suresh\Banner\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    /**
     * Execute the banner management page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute(): Page
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Suresh_Banner::banner');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Banners'));
        return $resultPage;
    }
}
