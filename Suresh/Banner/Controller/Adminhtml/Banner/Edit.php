<?php

namespace Suresh\Banner\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Edit extends Action
{
    /**
     * Execute the banner management page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute(): Page
    {
        $pageResult = $this->createPageResult();
        $title = $pageResult->getConfig()->getTitle();
        $title->prepend('Manage Bannars');
        $title->prepend('Edit Banner');

        return $pageResult;
    }

    private function createPageResult(): Page|ResultInterface
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
