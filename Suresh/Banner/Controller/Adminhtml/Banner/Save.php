<?php

declare(strict_types=1);

namespace Suresh\Banner\Controller\Adminhtml\Banner;

use Suresh\Banner\Model\BannerFactory;
use Suresh\Banner\Model\ResourceModel\Banner\CollectionFactory;
use Suresh\Banner\Model\ResourceModel\Banner as BannerResource;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action 
{
    public function __construct(
        Context $context,
        private BannerResource $resource,
        private BannerFactory $bannerFactory,
        private CollectionFactory $bannerCollectionFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        $data = $this->getRequest()->getPostValue();

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->bannerFactory->create();
            if (empty($data['banner_id'])) {
                $data['banner_id'] = null;
            } else {
                $banner = $model->load($data['banner_id']);
            }

            if (isset($banner) && $banner->getPriority() != $data['priority']) {
                if ($banner->getPriority() < $data['priority']) {
                    $between = [
                       'from' => $banner->getPriority(),
                        'to' => $data['priority']
                    ];
                    $start = $banner->getPriority();
                    $end = $data['priority'] - 1;
                } else {
                    $between = [
                        'from' => $data['priority'],
                        'to' => $banner->getPriority(),
                    ];
                    $end = $banner->getPriority();
                    $start = $data['priority'] + 1;
                }
                $collection = $this->bannerCollectionFactory
                                    ->create()
                                    ->addFieldToFilter('priority', $between)
                                    ->addFieldToFilter('banner_id', ['neq' => $data['banner_id']])
                                    ->setOrder('priority', 'ASC');

                foreach ($collection as $item) {
                    if($start > $end) {
                        break;
                    }
                    // echo $start;
                    // echo "-";
                    // echo $item->getBannerId();
                    // echo "<br/>";
                    $item->setPriority($start);
                    $item->save();
                    $start++;
                }
            }

            if (!empty($data['image'][0]['name']) && isset($data['image'][0]['tmp_name'])) {
                $data['image'] = $data['image'][0]['name'];
            } else {
                unset($data['image']);
            }

            $data['update_time'] = null;

            $model->setData($data);

            try {
                $this->resource->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the banner.'));
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $exception) {
                $this->messageManager->addExceptionMessage($exception);
            } catch (\Throwable $e) {
               $this->messageManager->addErrorMessage(__('Something went wrong while saving the banner.'));
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
