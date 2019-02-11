<?php
/**
 * Created by PhpStorm.
 * Date: 7/6/17
 */

namespace OK\Service;


use OK\Model\Amount;
use OK\Model\Cash\TransactionCancelResponse;
use OK\Model\Cash\Transaction;
use OK\Model\Network\Exception\NetworkException;

class Cash extends BaseService
{
    /**
     * Issue new transaction request.
     * @param $request Transaction request object
     * @return Transaction result
     * @throws NetworkException
     */
    public function request(Transaction $request) {
        return new Transaction($this->client->post('transactions', $request));
    }

    /**
     * Get an OK Transaction status.
     *
     * @param $guid string Already saved token of user
     * @return Transaction response
     * @throws NetworkException
     */
    public function status($guid) {
        $method = 'transactions/' . $guid . '/status';
        return new Transaction($this->client->get($method));
    }

    /**
     * Get a transaction
     * @param $guid string identifier of transaction
     * @return Transaction response
     * @throws NetworkException
     */
    public function get($guid) {
        $method = 'transactions/' . $guid . '';
        return new Transaction($this->client->get($method));
    }

    /**
     * Get a transaction by reference
     * @param $reference string reference
     * @return Transaction response
     * @throws NetworkException
     */
    public function getByReference($reference) {
        return new Transaction($this->client->get('transactions', [
            'reference' => $reference
        ]));
    }

    /**
     * Request an OK Payment QR code
     *
     * @param $guid string transaction identifier
     * @return string qrcode image
     */
    public function qr($guid) {
        return $this->client->getImage('transactions/' . $guid . '/qr.png');
    }

    /**
     * Cancel a transaction request
     * @param $guid string identifier of request
     * @return TransactionCancelResponse result
     * @throws NetworkException when error occurred or transaction could not be cancelled
     */
    public function cancel($guid) {
        return new TransactionCancelResponse($this->client->delete('transactions/' . $guid));
    }

    /**
     * Cancel a transaction request by reference
     * @param $reference string identifier of request
     * @return TransactionCancelResponse result
     * @throws NetworkException when error occurred or transaction could not be cancelled
     */
    public function cancelByReference($reference) {
        return new TransactionCancelResponse($this->client->delete('transactions', [
            'reference' => $reference
        ]));
    }

    /**
     * Refunds a previously initiated and closed and captured transaction.
     * @param $guid string identifier of transaction
     * @param $refundAmount Amount amount to refund in cents
     * @return string
     * @throws NetworkException
     */
    public function refund($guid, Amount $refundAmount) {
        return new Transaction(
            $this->client->post('transactions/' . $guid . '/refunds', ["amount" => $refundAmount])
        );
    }
}