<?php

namespace Okitcom\OkLibMagento\Cron;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteRepository;
use Okitcom\OkLibMagento\Helper\CheckoutHelper;
use Okitcom\OkLibMagento\Helper\ConfigHelper;
use Okitcom\OkLibMagento\Helper\QuoteHelper;
use Okitcom\OkLibMagento\Model\Checkout;
use \Psr\Log\LoggerInterface;

class TransactionStatus {

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CheckoutHelper
     */
    protected $checkoutHelper;

    /**
     * @var \Okitcom\OkLibMagento\Helper\QuoteHelper
     */
    protected $quoteHelper;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * TransactionStatus constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Okitcom\OkLibMagento\Helper\CheckoutHelper $checkoutHelper
     * @param \Okitcom\OkLibMagento\Helper\QuoteHelper $quoteHelper
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(LoggerInterface $logger,
                                CheckoutHelper $checkoutHelper,
                                QuoteHelper $quoteHelper,
                                CartRepositoryInterface $quoteRepository) {
        $this->logger = $logger;
        $this->checkoutHelper = $checkoutHelper;
        $this->quoteHelper = $quoteHelper;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Update transactions
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */

    public function execute() {
        $this->logger->info('Running OK transaction status check');

        $okCash = $this->checkoutHelper->getCashService();
        $transactions = $this->checkoutHelper->getAllPending();
//        $this->logger->info("Found " . $transactions->count() . " tx to update");
        $updated = 0;
        $completed = 0;
        $still_pending = 0;
        /** @var Checkout $item */
        foreach ($transactions->getItems() as $item) {
            // Get status

            $guid = $item->getGuid();
            $okResponse = $okCash->get($guid);

            try {
                if ($okResponse != null && $okResponse->state != $item->getState()) {
                    $item->setState($okResponse->state);
                    $item->save();

                    if ($okResponse->state == ConfigHelper::STATE_CHECKOUT_SUCCESS) {
                        $this->createOrder($item, $okResponse);
                        $completed++;
                    }

                    $updated++;
                } else {
                    $still_pending++;
                }
            } catch (\Exception $e) {
                $this->logger->error("Could not update OK transaction with id " . $item->getId(), $e);
            }

            // Mark NewPendingTrigger transactions as closed (if older than X time)
            //$this->logger->info("Transaction ID " . $item->id . " state " . $item->state);
        }

        if ($updated > 0) {
            $this->logger->info("Ran update on " . $transactions->count() . " transactions. (" . $updated . " updated, " . $completed . " completed, " . $still_pending . " still pending)");
        }
    }

    /**
     * Create an order from the checkout
     * @param Checkout $checkout
     * @param $okResponse
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Exception
     */
    private function createOrder(Checkout $checkout, $okResponse) {
        // process
        if ($checkout->getSalesOrderId() == null) {
            // update
            $quote = $this->quoteRepository->get($checkout->getQuoteId());
            $order = $this->quoteHelper->createOrder($quote, $okResponse);

            $checkout->setSalesOrderId($order->getEntityId());
            $checkout->save();
        }
    }

}