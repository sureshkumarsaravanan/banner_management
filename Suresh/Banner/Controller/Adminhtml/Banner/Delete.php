<?php

declare(strict_types=1);

namespace Suresh\Banner\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Suresh\Banner\Model\ResourceModel\Banner as BannerResource;
use Suresh\Banner\Model\BannerFactory;

class Delete extends Action
{
    /**
     * Delete constructor.
     * @param Context $context
     * @param BannerResource $resource
     * @param BannerFactory $bannerFactory
     */
    public function __construct(
        Context $context,
        private BannerResource $resource,
        private BannerFactory $bannerFactory
    ) {
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $bannerId = (int) $this->getRequest()->getParam('banner_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$bannerId) {
            $this->messageManager->addErrorMessage(__('We can\'t find a banner to delete'));
            return $resultRedirect->setPath('*/*/');
        }

        $model = $this->bannerFactory->create();

        try {
            $this->resource->load($model, $bannerId);
            $this->resource->delete($model);

            $this->messageManager->addSuccessMessage(__('The banner has been deleted.'));
        } catch (\Throwable $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $resultRedirect->setPath('*/*/');
    }
}
