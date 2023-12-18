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
use JcElectronics\ExactOrders\Modifiers\ModifierInterface;
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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\ServiceInputProcessor;
use Magento\Payment\Helper\Data;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    use FormatExternalOrderAddressTrait;
    use FormatExternalOrderDataTrait;

    public function __construct(
        private readonly MagentoOrderRepositoryInterface $orderRepository,
        private readonly OrderItemRepositoryInterface $orderItemRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly CompanyManagementInterface $companyManagement,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly ServiceInputProcessor $serviceInputProcessor,
        private readonly StoreRepositoryInterface $storeRepository,
        private readonly ProductRepositoryInterface $productRepository,
        protected readonly ExternalOrderFactory $externalOrderFactory,
        protected readonly AddressFactory $externalOrderAddressFactory,
        protected readonly ItemFactory $externalOrderItemFactory,
        private readonly AttachmentRepositoryInterface $attachmentRepository,
        private readonly AttachmentFactory $attachmentFactory,
        private readonly Config $config,
        private readonly OrderManagementInterface $orderManagement,
        private readonly Data $paymentHelper,
        protected readonly ScopeConfigInterface $scopeConfig,
        private readonly array $modifiers = []
    ) {
    }

    public function getById(string $id): ExternalOrderInterface
    {
        return $this->formatExternalOrderData(
            $this->orderRepository->get($id)
        );
    }

    public function getByIncrementId(string $id): ExternalOrderInterface
    {
        $collection = $this->orderRepository
            ->getList(
                $this->searchCriteriaBuilder
                    ->addFilter(OrderInterface::INCREMENT_ID, $id)
                    ->create()
            )
            ->getItems();

        if (count($collection) === 0) {
            throw NoSuchEntityException::singleField(OrderInterface::INCREMENT_ID, $id);
        }

        return $this->formatExternalOrderData(current($collection));
    }

    public function getByExternalId(string $id): ExternalOrderInterface
    {
        $collection = $this->orderRepository
            ->getList(
                $this->searchCriteriaBuilder
                    ->addFilter(OrderInterface::EXT_ORDER_ID, $id)
                    ->create()
            )
            ->getItems();

        if (count($collection) === 0) {
            throw NoSuchEntityException::singleField(OrderInterface::EXT_ORDER_ID, $id);
        }

        return $this->formatExternalOrderData(current($collection));
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
        /** @var OrderInterface $order */
        $order  = $this->processModifiers($order);
        $result = $order->getEntityId()
            ? $this->orderRepository->save($order)
            : $this->orderManagement->place($order);

        $this->saveAttachments($order);

        return (int) $result->getEntityId();
    }

    private function saveAttachments(OrderInterface $order): void
    {
        $attachments = $order->getExtensionAttributes()->getAttachments() ?? [];

        foreach ($attachments as $attachment) {
            try {
                $attachmentObject = $this->attachmentRepository->getByEntity(
                    (int) $result->getEntityId(),
                    AttachmentInterface::ENTITY_TYPE_ORDER
                );
            } catch (NoSuchEntityException) {
                /** @var AttachmentInterface $attachmentObject */
                $attachmentObject = $this->attachmentFactory->create();
            }

            $attachmentObject->setParentId((int) $result->getEntityId())
                ->setEntityTypeId(AttachmentInterface::ENTITY_TYPE_ORDER)
                ->setFileName($attachment['name'])
                ->setFileContent($attachment['file_data']);

            $this->attachmentRepository->save($attachmentObject);
        }
    }

    private function processModifiers($order)
    {
        $result = null;

        /** @var ModifierInterface $modifier */
        foreach ($this->modifiers as $modifier) {
            if (!$modifier->supports($order)) {
                continue;
            }

            $result = $modifier->process($order, $result);
        }

        return $result;
    }
}
