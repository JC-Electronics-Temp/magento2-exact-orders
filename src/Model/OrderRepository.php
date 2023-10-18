<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Api\OrderRepositoryInterface;
use JcElectronics\ExactOrders\Model\ExternalOrder\AddressFactory as ExternalOrderAddressFactory;
use JcElectronics\ExactOrders\Model\ExternalOrder\ItemFactory as ExternalOrderItemFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\AddressFactory;
use Magento\Sales\Model\Order\ItemFactory;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\PaymentFactory;
use Magento\Sales\Model\OrderFactory;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private readonly MagentoOrderRepositoryInterface $orderRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly ExternalOrderFactory $externalOrderFactory,
        private readonly OrderFactory $orderFactory,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly PaymentFactory $paymentFactory,
        private readonly ItemFactory $orderItemFactory,
        private readonly AddressRepositoryInterface $customerAddressRepository,
        private readonly AddressFactory $orderAddressFactory,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ExternalOrderAddressFactory $externalOrderAddressFactory,
        private readonly ExternalOrderItemFactory $externalOrderItemFactory
    ) {
    }

    /**
     * @param string $id
     *
     * @return ExternalOrderInterface
     * @throws LocalizedException
     */
    public function getById(string $id): ExternalOrderInterface
    {
        return $this->normalize(
            $this->orderRepository->get($id)
        );
    }

    /**
     * @param string $incrementId
     * @param bool   $normalize
     *
     * @return ExternalOrderInterface|OrderInterface
     * @throws LocalizedException
     */
    public function getByIncrementId(
        string $incrementId,
        bool $normalize = true
    ): ExternalOrderInterface|OrderInterface {
        $collection = $this->orderRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter(OrderInterface::INCREMENT_ID, $incrementId)
                ->create()
        );

        $items = $collection->getItems();

        if (!$items) {
            throw new LocalizedException(
                __('No order found with the specified increment ID.')
            );
        }

        return $normalize
            ? $this->normalize(current($items))
            : current($items);
    }

    /**
     * @param string $id
     *
     * @return ExternalOrderInterface
     * @throws LocalizedException
     */
    public function getByExternalId(string $id): ExternalOrderInterface
    {
        $collection = $this->orderRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter(OrderInterface::EXT_ORDER_ID, $id)
                ->create()
        );

        $items = $collection->getItems();
        
        if (!$items) {
            throw new LocalizedException(
                __('No order found with the specified external ID.')
            );
        }

        return $this->normalize(current($items));
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return array
     */
    public function getList(SearchCriteriaInterface $searchCriteria): array
    {
        $collection = $this->orderRepository->getList($searchCriteria);

        return $collection->getItems();
    }

    /**
     * @param ExternalOrderInterface $order
     *
     * @return int
     * @throws LocalizedException
     */
    public function save(
        ExternalOrderInterface $order
    ): int {
        try {
            $customer = $this->customerRepository->getById(
                $order->getData('magento_customer_id')
            );
        } catch (NoSuchEntityException | LocalizedException) {
            throw new LocalizedException(
                __('No Magento customer found for this order, so we can\'t store the order')
            );
        }

        try {
            /** @var Order $magentoOrder */
            $magentoOrder = $this->getByIncrementId($order->getMagentoIncrementId(), false);
        } catch (LocalizedException) {
            /** @var Order $magentoOrder */
            $magentoOrder = $this->orderFactory->create();
        }

        $this->setBaseOrderData($order, $magentoOrder, $customer)
            ->setCustomerData($order, $magentoOrder, $customer)
            ->addProductsToOrder($order, $magentoOrder)
            ->setOrderAddresses($order, $magentoOrder, $customer)
            ->setOrderPaymentInformation($order, $magentoOrder);

        $this->orderRepository->save($magentoOrder);

        $order->setData('id', $magentoOrder->getId());

        return (int) $magentoOrder->getId();
    }

    private function setBaseOrderData(
        ExternalOrderInterface $externalOrder,
        Order $order,
        CustomerInterface $customer
    ): self {
        $order->setBaseGrandTotal((float) $externalOrder->getGrandtotal())
            ->setBaseSubtotal((float) $externalOrder->getSubtotal())
            ->setGrandTotal((float) $externalOrder->getGrandtotal())
            ->setTotalPaid((float) $externalOrder->getGrandtotal())
            ->setSubtotal((float) $externalOrder->getSubtotal())
            ->setCreatedAt($externalOrder->getOrderDate())
            ->setUpdatedAt($externalOrder->getUpdatedAt())
            ->setStoreId($customer->getStoreId())
            ->setIncrementId($externalOrder->getMagentoIncrementId())
            ->setExtOrderId($externalOrder->getExtOrderId())
            ->setState($externalOrder->getState())
            ->setStatus($externalOrder->getState())
            ->setBaseTaxAmount((float) $externalOrder->getTaxAmount())
            ->setTaxAmount((float) $externalOrder->getTaxAmount())
            ->setBaseTotalDue(0)
            ->setTotalDue(0)
            ->setShippingMethod($externalOrder->getData('shipping_method') ?: 'Unknown')
            ->setShippingDescription($externalOrder->getData('shipping_method') ?: 'Unknown Shipping Method')
            ->setWeight(0)
            ->setIsVirtual(false)
            ->setEmailSent(true)
            ->setSendEmail(true)
            ->setGiftCards('[]')
            ->setData('is_external_order', !empty($externalOrder->getData('ext_order_id')));

        return $this;
    }

    private function setCustomerData(
        ExternalOrderInterface $externalOrder,
        Order $order,
        CustomerInterface $customer
    ): self {
        $customerData = $externalOrder->getData('billing_address');

        $order->setCustomerId($customer->getId())
            ->setCustomerEmail($customer->getEmail())
            ->setCustomerIsGuest(false)
            ->setCustomerGroupId($customer->getGroupId())
            ->setCustomerFirstname($customerData['firstname'])
            ->setCustomerLastname($customerData['lastname'])
            ->setCustomerMiddlename($customerData['middlename'])
            ->setCustomerGender($customer->getGender())
            ->setCustomerPrefix($customerData['prefix'])
            ->setCustomerSuffix($customerData['suffix'])
            ->setCustomerNoteNotify(false)
            ->setData('ext_customer_id', $externalOrder->getExternalCustomerId());

        return $this;
    }

    private function addProductsToOrder(
        ExternalOrderInterface $externalOrder,
        Order $order
    ): self {
        foreach ($externalOrder->getItems() as $item) {
            $product   = $this->getProductBySku($item->getData('sku'));
            $orderItem = $this->orderItemFactory->create();
            $orderItem->setName($item->getData('name'))
                ->setSku($item->getData('sku'))
                ->setBasePrice((float) $item->getPrice())
                ->setPrice((float) $item->getPrice())
                ->setOriginalPrice((float) $item->getPrice())
                ->setBaseOriginalPrice((float) $item->getPrice())
                ->setBaseRowTotal((float) $item->getRowTotal())
                ->setRowTotal((float) $item->getRowTotal())
                ->setBaseTaxAmount((float) $item->getTaxAmount())
                ->setTaxAmount((float) $item->getTaxAmount())
                ->setQtyOrdered((int)$item->getQty())
                ->setCreatedAt($externalOrder->getOrderDate())
                ->setStoreId($order->getStoreId())
                ->setBaseDiscountAmount((float) $item->getDiscountAmount())
                ->setDiscountAmount((float) $item->getDiscountAmount());

            if ($product instanceof ProductInterface) {
                $orderItem->setProductId($product->getId());
            }

            $order->addItem($orderItem);
        }

        return $this;
    }

    private function setOrderAddresses(
        ExternalOrderInterface $externalOrder,
        Order $order,
        CustomerInterface $customer
    ): self {
        $order->setBillingAddress(
            $this->createOrderAddress(
                $externalOrder,
                $customer,
                AbstractAddress::TYPE_BILLING
            )
        );

        $order->setShippingAddress(
            $this->createOrderAddress(
                $externalOrder,
                $customer,
                AbstractAddress::TYPE_SHIPPING
            )
        );
        return $this;
    }

    private function createOrderAddress(
        ExternalOrderInterface $externalOrder,
        CustomerInterface $customer,
        string $addressType
    ): OrderAddressInterface {
        $addressData = $externalOrder->getData($addressType . '_address');
        try {
            $address = $this->customerAddressRepository->getById(
                $addressType === AbstractAddress::TYPE_BILLING
                    ? $customer->getDefaultBilling()
                    : $customer->getDefaultShipping()
            );
        } catch (LocalizedException) {
            $address = null;
        }

        /** @var OrderAddressInterface $orderAddress */
        $orderAddress = $this->orderAddressFactory->create();
        $orderAddress->setAddressType($addressType);
        $orderAddress->setFirstname($addressData['firstname'])
            ->setLastname($addressData['lastname'])
            ->setMiddlename($addressData['middlename'])
            ->setPrefix($addressData['prefix'])
            ->setSuffix($addressData['suffix'])
            ->setCompany($addressData['company'])
            ->setStreet($addressData['street'])
            ->setPostcode($addressData['postcode'])
            ->setCity($addressData['city'])
            ->setCountryId($addressData['country'])
            ->setTelephone($address ? $address->getTelephone() : '-')
            ->setFax($addressData['fax']);

        return $orderAddress;
    }

    private function setOrderPaymentInformation(
        ExternalOrderInterface $externalOrder,
        Order $order
    ): self {
        $store = $order->getStore();

        $order->setOrderCurrencyCode($store->getCurrentCurrencyCode())
            ->setBaseCurrencyCode($store->getBaseCurrencyCode())
            ->setGlobalCurrencyCode($store->getBaseCurrencyCode())
            ->setStoreCurrencyCode($store->getCurrentCurrencyCode());

        /** @var Payment $payment */
        $payment = $this->paymentFactory->create();
        $payment->setBaseShippingAmount((float) $externalOrder->getData('base_shipping_amount'))
            ->setShippingAmount((float) $externalOrder->getData('shipping_amount'))
            ->setMethod($externalOrder->getData('payment_method') ?? 'Unknown')
            ->setPoNumber($externalOrder->getData('po_number'));

        $order->setPayment($payment);

        return $this;
    }

    private function getProductBySku(string $sku): ?ProductInterface
    {
        try {
            return $this->productRepository->get($sku);
        } catch (NoSuchEntityException) {
            return null;
        }
    }

    private function normalize(Order $order): ExternalOrderInterface
    {
        $externalOrder = $this->externalOrderFactory->create();
        $externalOrder->setData(
            [
                "order_id" => $order->getId(),
                "invoice_ids" => array_map(
                    fn (InvoiceInterface $invoice) => (int) $invoice->getEntityId(),
                    $order->getInvoiceCollection()->getItems()
                ),
                "magento_order_id" => $order->getId(),
                "magento_customer_id" => $order->getCustomerId(),
                "base_grandtotal" => (string)$order->getBaseGrandTotal(),
                "base_subtotal" => (string)$order->getBaseSubtotal(),
                "grandtotal" => (string) $order->getGrandTotal(),
                "subtotal" => (string) $order->getSubtotal(),
                "state" => $order->getState(),
                "shipping_method" => $order->getShippingMethod(),
                'shipping_address' => $this->normalizeAddress($order->getShippingAddress()),
                'billing_address' => $this->normalizeAddress($order->getBillingAddress()),
                "payment_method" => $order->getPayment()->getMethod(),
                "base_discount_amount" => $order->getBaseDiscountAmount() ?? '0.0000',
                "discount_amount" => $order->getDiscountAmount() ?? '0.0000',
                "order_date" => $order->getCreatedAt(),
                "base_tax_amount" => $order->getBaseTaxAmount() ?? '0.0000',
                "tax_amount" => $order->getTaxAmount() ?? '0.0000',
                "base_shipping_amount" => $order->getBaseShippingAmount() ?? '0.0000',
                "shipping_amount" => $order->getShippingAmount() ?? '0.0000',
                'items' => array_map(
                    function (OrderItemInterface $item) {
                        $externalItem = $this->externalOrderItemFactory->create();
                        $externalItem->setData(
                            [
                                'orderitem_id' => $item->getEntityId(),
                                'order_id' => $item->getOrderId(),
                                'name' => $item->getName(),
                                'sku' => $item->getSku(),
                                'base_price' => (string)$item->getBasePrice(),
                                'price' => (string)$item->getPrice(),
                                'base_row_total' => (string)$item->getBaseRowTotal(),
                                'row_total' => (string)$item->getRowTotal(),
                                'base_tax_amount' => (string)$item->getBaseTaxAmount(),
                                'tax_amount' => (string)$item->getTaxAmount(),
                                'qty' => $item->getQtyOrdered(),
                                'base_discount_amount' => (string)$item->getBaseDiscountAmount(),
                                'discount_amount' => (string)$item->getDiscountAmount(),
                                'additionalData' => []
                            ]
                        );

                        return $externalItem;
                    },
                    $order->getAllItems()
                ),
                "magento_increment_id" => $order->getIncrementId(),
                "updated_at" => $order->getUpdatedAt(),
                "additional_data" => [],
                "attachments" => []
            ]
        );

        return $externalOrder;
    }

    private function normalizeAddress(
        OrderAddressInterface $address
    ): AddressInterface {
        $externalAddress = $this->externalOrderAddressFactory->create();
        $externalAddress->setData(
            [
                'orderaddress_id' => $address->getEntityId(),
                'firstname' => $address->getFirstname(),
                'lastname' => $address->getLastname(),
                'company' => $address->getCompany(),
                'street' => $address->getStreet(),
                'postcode' => $address->getPostcode(),
                'city' => $address->getCity(),
                'country' => $address->getCountryId(),
                'additional_data' => []
            ]
        );

        return $externalAddress;
    }
}
