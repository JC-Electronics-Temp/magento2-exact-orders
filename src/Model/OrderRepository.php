<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Api\OrderRepositoryInterface;
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
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
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
        private readonly ProductRepositoryInterface $productRepository
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
        try {
            $order = $this->orderRepository->get($id);
        } catch (NoSuchEntityException) {
            throw new LocalizedException(
                __('No order found with the specified ID.')
            );
        }

        return $this->normalizeOrder($order);
    }

    /**
     * @param string $incrementId
     *
     * @return ExternalOrderInterface
     * @throws LocalizedException
     */
    public function getByIncrementId(string $incrementId): ExternalOrderInterface
    {
        $collection = $this->orderRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter(OrderInterface::INCREMENT_ID, $incrementId)
                ->create()
        );

        if (!$collection->getItems()) {
            throw new LocalizedException(
                __('No order found with the specified increment ID.')
            );
        }

        return $this->normalizeOrder(
            current($collection->getItems())
        );
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

        if (!$collection->getItems()) {
            throw new LocalizedException(
                __('No order found with the specified external ID.')
            );
        }

        return $this->normalizeOrder(
            current($collection->getItems())
        );
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return array
     */
    public function getList(SearchCriteriaInterface $searchCriteria): array
    {
        $collection = $this->orderRepository->getList($searchCriteria);

        return array_reduce(
            $collection->getItems(),
            fn (OrderInterface $order) => $this->normalizeOrder($order),
            []
        );
    }

    /**
     * @param ExternalOrderInterface $externalOrder
     *
     * @return ExternalOrderInterface
     * @throws LocalizedException
     */
    public function save(
        ExternalOrderInterface $externalOrder
    ): ExternalOrderInterface {
        try {
            $customer = $this->customerRepository->getById(
                $externalOrder->getData('magento_customer_id')
            );
        } catch (NoSuchEntityException | LocalizedException) {
            throw new LocalizedException(
                __('No Magento customer found for this order, so we can\'t store the order')
            );
        }

        /** @var Order $order */
        $order = $this->orderFactory->create();
        $this->setBaseOrderData($externalOrder, $order, $customer)
            ->setCustomerData($externalOrder, $order, $customer)
            ->addProductsToOrder($externalOrder, $order)
            ->setOrderAddresses($externalOrder, $order, $customer)
            ->setOrderPaymentInformation($externalOrder, $order);
        
        $this->orderRepository->save($order);

        $externalOrder->setData('id', $order->getId());

        return $externalOrder;
    }

    private function normalizeOrder(
        OrderInterface $order
    ): ExternalOrderInterface {
        /** @var ExternalOrderInterface $externalOrder */
        $externalOrder = $this->externalOrderFactory->create();
        $externalOrder->normalizeOrder($order);

        return $externalOrder;
    }

    private function setBaseOrderData(
        ExternalOrderInterface $externalOrder,
        Order $order,
        CustomerInterface $customer
    ): self {
        $order->setBaseGrandTotal($externalOrder->getData('base_grandtotal'))
            ->setBaseSubtotal($externalOrder->getData('base_subtotal'))
            ->setGrandTotal($externalOrder->getData('grandtotal'))
            ->setTotalPaid($externalOrder->getData('grandtotal'))
            ->setSubtotal($externalOrder->getData('subtotal'))
            ->setCreatedAt($externalOrder->getData('order_date'))
            ->setUpdatedAt($externalOrder->getData('updated_at'))
            ->setStoreId($customer->getStoreId())
            ->setIncrementId($externalOrder->getData('magento_increment_id'))
            ->setExtOrderId($externalOrder->getData('ext_order_id'))
            ->setState($externalOrder->getData('state'))
            ->setStatus($externalOrder->getData('state'))
            ->setBaseDiscountAmount($externalOrder->getData('base_discount_amount'))
            ->setDiscountAmount($externalOrder->getData('discount_amount'))
            ->setBaseTaxAmount($externalOrder->getData('base_tax_amount'))
            ->setTaxAmount($externalOrder->getData('tax_amount'))
            ->setBaseShippingAmount($externalOrder->getData('base_shipping_amount'))
            ->setShippingAmount($externalOrder->getData('shipping_amount'))
            ->setBaseTotalDue(0)
            ->setTotalDue(0)
            ->setShippingMethod($externalOrder->getData('shipping_method'))
            ->setShippingDescription($externalOrder->getData('shipping_method'))
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
            ->setData('ext_customer_id', $externalOrder->getData('external_customer_id'));

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
                ->setBasePrice($item->getData('base_price'))
                ->setPrice($item->getData('price'))
                ->setOriginalPrice($item->getData('price'))
                ->setBaseOriginalPrice($item->getData('base_price'))
                ->setBaseRowTotal($item->getData('base_row_total'))
                ->setRowTotal($item->getData('row_total'))
                ->setBaseTaxAmount($item->getData('base_tax_amount'))
                ->setTaxAmount($item->getData('tax_amount'))
                ->setQtyOrdered($item->getData('qty'))
                ->setCreatedAt($externalOrder->getData('order_date'))
                ->setStoreId($order->getStoreId())
                ->setBaseDiscountAmount($item->getData('base_discount_amount'))
                ->setDiscountAmount($item->getData('discount_amount'));

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
        $payment->setBaseShippingAmount($externalOrder->getData('base_shipping_amount'))
            ->setShippingAmount($externalOrder->getData('shipping_amount'))
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
}
