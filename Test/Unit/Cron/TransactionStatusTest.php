<?php
/**
 * Created by PhpStorm.
 * Date: 1/28/18
 */

namespace Okitcom\OkLibMagento\Test\Unit\Cron;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Quote\Model\QuoteRepository;
use OK\Service\Cash;
use Okitcom\OkLibMagento\Cron\TransactionStatus;
use Okitcom\OkLibMagento\Helper\CheckoutHelper;
use Okitcom\OkLibMagento\Helper\QuoteHelper;
use Okitcom\OkLibMagento\Model\Resource\Checkout\Collection;

class TransactionStatusTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var TransactionStatus
     */
    protected $transactionStatus;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $checkoutHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteRepository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $okService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionMock;

    protected function setUp() {
        parent::setUp();

        $this->checkoutHelper = $this->getMockBuilder(CheckoutHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->quoteRepository = $this->getMockBuilder(QuoteRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->quoteHelper = $this->getMockBuilder(QuoteHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->collectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->configureOKService();
        $this->setupCollectionMock();

        $arguments = array(
            'checkoutHelper' => $this->checkoutHelper,
            'quoteRepository' => $this->quoteRepository,
            'quoteHelper' => $this->quoteHelper
        );

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->transactionStatus = $objectManagerHelper->getObject(TransactionStatus::class, $arguments);
    }

    private function setupCollectionMock() {
        $this->checkoutHelper->expects($this->once())
            ->method('getAllPending')
            ->willReturn($this->returnValue($this->collectionMock));
    }

    private function configureOKService() {
        $this->okService = $this->getMockBuilder(Cash::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutHelper->method('getCashService')
            ->willReturn($this->returnValue($this->okService));
    }

    private function setTransactionsShouldBeProcessIntoQuote() {
        $this->quoteRepository->method('get')
            ->willReturn($this->returnValue(null));
    }

    public function testTransactionsEmpty() {
//        $this->collectionMock->expects($this->once())
//            ->method('getItems')
//            ->willReturn($this->returnValue([]));
//
//        $this->quoteRepository->expects($this->never())
//            ->method('get');
//        $this->okService->expects($this->never())
//            ->method('get');
//
//        $this->transactionStatus->execute();
    }


}