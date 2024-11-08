<?php

namespace Suresh\Banner\Model\Banner;

use Magento\Ui\DataProvider\ModifierPoolDataProvider;
use Suresh\Banner\Model\Banner;
use Suresh\Banner\Model\BannerFactory;
use Suresh\Banner\Model\ResourceModel\Banner as BannerResource;
use Suresh\Banner\Model\ResourceModel\Banner\CollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filesystem\Driver\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

class DataProvider extends ModifierPoolDataProvider
{

    /**
     * @var array
     */
    private array $loadedData;

    /**
     * @var ReadInterface 
     */
    private ReadInterface $mediaDirectory;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        private BannerResource $resource,
        private BannerFactory $bannerFactory,
        private RequestInterface $request,
        Filesystem $filesystem,
        private StoreManagerInterface $storeManager,
        private Mime $mime,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
        $this->collection = $collectionFactory->create();
        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
    }

        /**
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData(): array
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $banner = $this->getCurrentBanner();
        $bannerData = $banner->getData();

        $image = $bannerData['image'];
        
        if (!$image) {
            $this->loadedData[$banner->getId()] = $bannerData;
            return $this->loadedData;
        }

        $imgDir = 'tmp/imageUploader/images';
        $baseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        $fullImagePath = $this->mediaDirectory->getAbsolutePath($imgDir) . '/' . $image;
        $imageUrl = $baseUrl . $imgDir . '/' . $image;
        $stat = $this->mediaDirectory->stat($fullImagePath);
        
        $bannerData['image'] = null;
        $bannerData['image'][0]['url'] = $imageUrl;
        $bannerData['image'][0]['name'] = $image;
        $bannerData['image'][0]['size'] = $stat['size'];
        $bannerData['image'][0]['type'] = $this->mime->getMimeType($fullImagePath);

        $this->loadedData[$banner->getId()] = $bannerData;

        return $this->loadedData;
    }

    /**
     * @return Banner
     */
    private function getCurrentBanner(): Banner
    {
        $bannerId = $this->getBannerId();
        $banner = $this->bannerFactory->create();
        if (!$bannerId) {
            return $banner;
        }

        $this->resource->load($banner, $bannerId);

        return $banner;
    }

    /**
     * @return int
     */
    private function getBannerId(): int
    {
        return (int) $this->request->getParam($this->getRequestFieldName());
    }
}