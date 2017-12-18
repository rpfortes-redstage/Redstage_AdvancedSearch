<?php

namespace Redstage\AdvancedSearch\Controller\Search;


use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;

class Index extends Action
{
    protected $_jsonFactory;

    protected $_collectionFactory;

    protected $productCollection;

    protected $catalogProductVisibility;

    protected $stockFilter;

    public function __construct(
        Context $context,
        JsonFactory $resultPageFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\CatalogInventory\Helper\Stock $stockFilter,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor

    )
    {
        $this->_jsonFactory = $resultPageFactory;
        parent::__construct($context);
        $this->productCollection = $productCollection;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->stockFilter = $stockFilter;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $result = $this->_jsonFactory->create();
        $value = $this->getRequest()->getParam('name');
        
        /*@TODO
        Refator the search
        */
        /*$collection = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')
                    ->addAttributeToSelect(['name', 'price', 'image', 'url_key'])
                    ->addAttributeToFilter('name', array('like' => '%'.$value.'%'))
                    ->setPageSize(10, 1);
        */

        $collection = $this->productCollection->create();
        $this->extensionAttributesJoinProcessor->process($collection);
//        $collection->addAttributeToSelect(['name']);
        $collection->addAttributeToSelect('*');
        $collection->joinAttribute('url_key', 'catalog_product/url_key', 'entity_id', null, 'inner');
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        $this->stockFilter->addInStockFilterToCollection($collection);

//        $collection->addUrlRewrite();
        $collection->addAttributeToFilter('name', ['like' => '%'.$value.'%']);
        $collection->load();

//        echo '<pre>';
//        print_r($collection->getData()); exit;

        if(!empty($this->getRequest()->getQueryValue())){
            $result->setData(['data' => $collection->getData()]);
        }


        /*
        $collection = $this->productCollection->create();
        $this->extensionAttributesJoinProcessor->process($collection);

        $collection->addAttributeToSelect('*');
        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');

        $collection->setCurPage(1);
        $collection->setPageSize(100);
        $collection->load();

        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

    return $searchResult;
         */


        return $result;
    }

}