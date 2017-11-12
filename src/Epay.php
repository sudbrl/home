<?php
namespace Ktmcodelabs\Esewa;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

/**
*  Esewa Payment Class
*
*  Use this section to define what this class is doing, the PHPDocumentator will use this
*  to automatically generate an API documentation using this information.
*
*  @author KtmCodeLabs
*/

class Epay{
	/**  @var string $pay_url URL FOR payment */
	private $pay_url = '';

	/**  @var float $amt Amount of the product/item - required */
	private $amt;

	/**  @var float $txAmt TAX Amount on the product/item */
	private $txAmt = 0;

	/**  @var float $psc Service charge */
	private $psc = 0; 

	/**  @var float $pdc Delibvery Charge */
	private $pdc = 0;

	/**  @var float $tAmt Total amount of the product including tax and charges - required  */
	private $tAmt;

	/** @var string scd Merchant/Service code provided by Esewa - required */
	private $scd;

	/** @var string pid A unique ID representing product - required */
	private $pid;

	/** @var string su Success URI - required */
	private $su;

	/** @var string fu Failure URI - required */
	private $fu;

	/** @var string vu Transaction verification url */

	/** @var string response Response for the request */
	private $res = '';


	/**
      * Constructor Method
      *
      * Initiate
      *
      */
	public function __construct(){
	}

	/**
      * Make Payment
      *
      * Proceed to make payment
      *
      * @param Request $request Request post string
      *
      * @return Response from the payment gateway
      */
	public function pay(Request $request){
		if(!$this->validate($request)){
			return $this->response;
		};

	}

	/**
      * Transaction verification
      *
      * Verify transaction after successful transaction
      *
      * @param Request $request Request post string
      *
      * @return success or failure
      */
	public function verifyTxn(){

	}

	/**
      * Validation
      *
      * Validate required data beofre making payment
      *
      * @param Request $request Request post string
      *
      * @return success or failure
      */
	public function validate(Request $request){
		$fillable = array('amt','tAmt','scd','pid','su','fu');
		$tmp = array_keys ( ( array ) $request );
		$diff = array_diff ( $fillable, $tmp );
		if (sizeof ( $diff ) > 0) {
			$this->response['error'] = true;
			$this->response['success'] = false;
			$this->response['errMessage'] = 'Insufficient data. Required: '.implode(',',$diff);
			return false;
		}
		return true;
	}
}