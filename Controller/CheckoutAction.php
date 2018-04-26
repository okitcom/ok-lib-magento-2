<?php
/**
 * Created by PhpStorm.
 * Date: 8/12/17
 */

namespace Okitcom\OkLibMagento\Controller;


use Magento\Framework\App\Action\Action;
use Magento\Quote\Model\Quote;
use OK\Builder\AttributeBuilder;
use OK\Builder\LineItemBuilder;
use OK\Builder\TransactionBuilder;
use OK\Model\Amount;
use OK\Model\Attribute;
use Okitcom\OkLibMagento\Setup\InstallData;

abstract class CheckoutAction extends Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    /**
     * @var \Okitcom\OkLibMagento\Model\CheckoutFactory
     */
    protected $checkoutFactory;

    /**
     * @var \Okitcom\OkLibMagento\Helper\ConfigHelper
     */
    protected $configHelper;

    /**
     * @var \Okitcom\OkLibMagento\Helper\CheckoutHelper
     */
    protected $checkoutHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Okitcom\OkLibMagento\Helper\QuoteHelper
     */
    protected $quoteHelper;

    /**
     * @var \Magento\Framework\Math\Random
     */
    private $mathRandom;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Checkout\Model\Session $session
     * @param \Okitcom\OkLibMagento\Model\CheckoutFactory $checkoutFactory
     * @param \Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper
     * @param \Okitcom\OkLibMagento\Helper\CheckoutHelper $checkoutHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Okitcom\OkLibMagento\Helper\QuoteHelper $quoteHelper
     * @param \Magento\Framework\Math\Random $mathRandom
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Session $session,
        \Okitcom\OkLibMagento\Model\CheckoutFactory $checkoutFactory,
        \Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper,
        \Okitcom\OkLibMagento\Helper\CheckoutHelper $checkoutHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Okitcom\OkLibMagento\Helper\QuoteHelper $quoteHelper,
        \Magento\Framework\Math\Random $mathRandom
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->session = $session;
        $this->checkoutFactory = $checkoutFactory;
        $this->configHelper = $configHelper;
        $this->checkoutHelper = $checkoutHelper;
        $this->customerSession = $customerSession;
        $this->quoteHelper = $quoteHelper;
        $this->mathRandom = $mathRandom;
        parent::__construct($context);
    }

    /**
     * Process a quote into an OK request. Performs all necessary operations in Magento backend.
     * @param Quote|null $quote the quote to process. Leave null to get the current session quote, if any.
     * @return array|null
     */
    protected function requestCash(Quote $quote = null) {
        // get data
        if ($quote == null) {
            $referenceQuote = $this->session->getQuote();
            if ($referenceQuote == null) {
                return null;
            }

            $quote = $this->quoteHelper->createEmptyQuote(); // load empty cart quote
            $quote->setStoreId($referenceQuote->getStoreId());

            $quote->merge($referenceQuote)
                ->setTotalsCollectedFlag(false)
                ->collectTotals()
                ->save();
        }

        $totalPrice = $quote->getBaseGrandTotal();

        if ($totalPrice > 0) {
            $shippingMethod = $this->configHelper->getCheckoutConfig("default_shipping_method");
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setCountryId("NL"); // TODO: Change for estimation
            $shippingAddress->setPostcode("2611RM");
            $shippingAddress->setShippingMethod($shippingMethod)
                ->setCollectShippingRates(true)
                ->collectShippingRates();

            $carrier = explode("_", $shippingMethod)[0];

            $shippingPrice = 0;
            $rates = $shippingAddress->getGroupedAllShippingRates();
            if (isset($rates[$carrier])) {
                $shippingPrice = $rates[$carrier][0]->getPrice();
            }

            $totalAmount = $totalPrice + $shippingPrice;

            $externalIdentifier = $this->mathRandom->getRandomString(24);
            $checkout = $this->checkoutFactory->create();
            $checkout->setExternalId($externalIdentifier);
            $checkout->setQuoteId($quote->getId());
            $checkout->save();

            // create object
            $ok = $this->checkoutHelper->getCashService();

            $redirectUrl = $this->_url->getUrl("oklib/callback/cash", [
                "_secure" => true,
                "transaction" => $externalIdentifier
            ]);

            $transactionBuilder = (new TransactionBuilder())
                ->setReference($quote->getId())
                ->setRedirectUrl($redirectUrl)
                ->setAmount(Amount::fromEuro($totalAmount))
                ->setPermissions("TriggerPaymentInitiation")
                ->addAttribute(
                    (new AttributeBuilder())
                        ->setKey("name")
                        ->setLabel("Name")
                        ->setType(Attribute::TYPE_NAME)
                        ->setRequired(true)
                        ->build()
                )
                ->addAttribute(
                    (new AttributeBuilder())
                        ->setKey("email")
                        ->setLabel("Email")
                        ->setType(Attribute::TYPE_EMAIL)
                        ->setRequired(true)
                        //->setVerified(true)
                        ->build()
                )
                ->addAttribute(
                    (new AttributeBuilder())
                        ->setKey("address")
                        ->setLabel("Address")
                        ->setType(Attribute::TYPE_ADDRESS)
                        ->setRequired(true)
                        ->build()
                )
                ->addAttribute(
                    (new AttributeBuilder())
                        ->setKey("phone")
                        ->setLabel("Phone")
                        ->setType(Attribute::TYPE_PHONENUMBER)
                        ->setRequired(true)
                        //->setVerified(true)
                        ->build()
                );

            if ($this->configHelper->getCheckoutConfig("ask_note")) {
                // Ask user for a note
                $transactionBuilder->addAttribute(
                    (new AttributeBuilder())
                        ->setKey("note")
                        ->setLabel("Note")
                        ->setType(Attribute::TYPE_STRING)
                        ->setRequired(false)
                        ->build()
                );
            }

            $initiation = false;
            if ($this->customerSession->isLoggedIn()) {
                $token = $this->customerSession->getCustomer()->getData(InstallData::OK_TOKEN);
                if ($token != null) {
                    $transactionBuilder->setInitiationToken($token);
                }
            }

            /** @var \Magento\Quote\Model\Quote\Item $item */
            foreach ($quote->getAllItems() as $item) {
//                    echo  $item->getQty() . " " . $item->getName() . " " . $item->getPrice() . " - " . $item->getDiscountAmount() . " Calc: " . Amount::fromEuro($item->getPrice() - ($item->getDiscountAmount() / $item->getQty()))->getEuro() . "\n";
                $itemPrice = Amount::fromEuro(($item->getRowTotal() + $item->getTaxAmount()) / $item->getQty());
                $tax = Amount::fromEuro($item->getTaxPercent());
                $lineItemBuilder = (new LineItemBuilder())
                    ->setQuantity($item->getQty())
                    ->setProductCode($item->getSku())
                    ->setDescription($item->getName())
                    ->setAmount($itemPrice)
                    ->setVat($tax->getCents())
                    ->setCurrency("EUR");
                if ($item->getDiscountAmount() > 0) {
                    $lineItemBuilder->addSubItem(
                        (new LineItemBuilder())
                            ->setQuantity(1)
                            ->setProductCode("DISCOUNT")
                            ->setAmount(Amount::fromEuro(- $item->getDiscountAmount()))
                            ->setVat(0)
                            ->setDescription("Magento discount")
                            ->setCurrency("EUR")
                            ->build()
                    );
                }
                $transactionBuilder->addLineItem(
                       $lineItemBuilder->build()
                );
            }

            if ($shippingPrice > 0) {
                // Add shipping as a line item.
                $transactionBuilder->addLineItem(
                    (new LineItemBuilder())
                    ->setQuantity(1)
                    ->setProductCode("shipping_" . $shippingMethod)
                    ->setDescription("Shipping")
                    ->setAmount(Amount::fromEuro($shippingPrice))
                    ->setVat(0)
                    ->setCurrency("EUR")
                    ->build()
                );
            }
//            print_r($transactionBuilder->build());
//            echo "\nTOTAL " . $totalAmount;
//die();

            $response = $ok->request($transactionBuilder->build());

            $checkout->setGuid($response->guid);
            $checkout->setOkTransactionId($response->id);
            $checkout->setState($response->state);
            $checkout->save();

            return [
                "guid" => $response->guid,
                "initiation" => $initiation
            ];
        }

        return null;
    }

    protected function json(array $data, $responseCode = 200) {
        $result = $this->resultJsonFactory->create();
        $result->setData($data);
        $result->setHttpResponseCode($responseCode);
        return $result;
    }

    protected function getOkLibEnvironment() {
        return $this->configHelper->getOkLibEnvironment();
    }

    protected function getLocale() {
        return $this->configHelper->getLocale();
    }

}