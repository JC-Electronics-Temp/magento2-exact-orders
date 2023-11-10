<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\AttachmentRepositoryInterface;
use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Api\OrderRepositoryInterface;
use JcElectronics\ExactOrders\Model\ExternalOrder\AddressFactory;
use JcElectronics\ExactOrders\Model\ExternalOrder\ItemFactory;
use JcElectronics\ExactOrders\Traits\CustomerInformationTrait;
use JcElectronics\ExactOrders\Traits\FormatExternalOrderAddressTrait;
use JcElectronics\ExactOrders\Traits\FormatExternalOrderDataTrait;
use JcElectronics\ExactOrders\Traits\FormatOrderDataTrait;
use JcElectronics\ExactOrders\Traits\StoreInformationTrait;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Company\Api\CompanyManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Webapi\ServiceInputProcessor;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    use CustomerInformationTrait;
    use FormatOrderDataTrait;
    use FormatExternalOrderAddressTrait;
    use FormatExternalOrderDataTrait;
    use StoreInformationTrait;

    public function __construct(
        private readonly MagentoOrderRepositoryInterface $orderRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly CompanyManagementInterface $companyManagement,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly ServiceInputProcessor $serviceInputProcessor,
        private readonly StoreRepositoryInterface $storeRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ExternalOrderFactory $externalOrderFactory,
        private readonly AddressFactory $externalOrderAddressFactory,
        private readonly ItemFactory $externalOrderItemFactory,
        private readonly AttachmentRepositoryInterface $attachmentRepository,
        private readonly AttachmentFactory $attachmentFactory,
        private readonly Config $config
    ) {
    }

    public function getById(string $id): ExternalOrderInterface
    {
        return $this->formatExternalOrderData(
            $this->orderRepository->get($id)
        );
    }

    public function getByIncrementId(string $incrementId): ExternalOrderInterface
    {
        return $this->formatExternalOrderData(
            current(
                $this->orderRepository->getList(
                    $this->searchCriteriaBuilder
                        ->addFilter(OrderInterface::INCREMENT_ID, $incrementId)
                        ->create()
                )->getItems()
            )
        );
    }

    public function getByExternalId(string $id): ExternalOrderInterface
    {
        return $this->formatExternalOrderData(
            current(
                $this->orderRepository->getList(
                    $this->searchCriteriaBuilder
                        ->addFilter(OrderInterface::EXT_ORDER_ID, $id)
                        ->create()
                )->getItems()
            )
        );
    }

    public function getList(SearchCriteriaInterface $searchCriteria): array
    {
        return array_map(
            fn (OrderInterface $item) => $this->formatExternalOrderData($item),
            $this->orderRepository->getList($searchCriteria)->getItems()
        );
    }

    public function save(ExternalOrderInterface $order): int
    {
        $result = $this->orderRepository->save(
            $this->formatOrderData($order->getData())
        );

        foreach ($order->getAttachments() as $attachment) {
            /** @var AttachmentInterface $attachmentObject */
            $attachmentObject = $this->attachmentFactory->create();
            $attachmentObject->setParentId((int) $result->getEntityId())
                ->setEntityTypeId(AttachmentInterface::ENTITY_TYPE_ORDER)
                ->setFileName($attachment['name'])
                ->setFileContent($attachment['file_data']);

            $this->attachmentRepository->save($attachmentObject);
        }

        return (int) $result->getEntityId();
    }
}
