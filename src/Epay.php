<?php
namespace Ktmcodelabs\Esewa;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

/**
*  Esewa Payment Class
*
*  esewaEpay is PHP library designed to simplify the task of making online payment via Esewa service
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
	private $vu;

	/** @var string response Response for the request */
	private $res = '';


	/**
      * Constructor Method
      *
      * Initiate
      *
      */
	public function __construct($mcode){
		$this->scd = $mcode;
	}

	/**
      * Set URLs
      *
      * Set payment url and response urls (success,failure)
      *
      * @param Request $request Request post string
      *
      * @return Response from the payment gateway
      */
	public function setUrl(array $urls){
		$rUrls = array('pay_url','su','fu','vu');
		foreach($rUrls as $k=>$v){
			if(array_key_exists($k, $urls)){
				$this->$k = $urls[$k];
			}
		}
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

		$this->tAmt = $this->amt + $this->txAmt + $this->psc + $this->pdc;
		$postparams = array(
			'amt'=>$this->amt,
			'txAmt'=>$this->txAmt,
			'psc'=>$this->psc,
			'pdc'=>$this->pdc,
			'tAmt'=>$this->tAmt,
			'scd'=>$this->$scd,
			'pid'=>$this->pid,
			'su'=>$this->su,
			'fu'=>$this->fu
		);

		$client = new Client();
		$response = $client->request('POST', $this->pay_url, [
		    'form_params' => $postparams
		]);

		
		$this->response['statusCode'] = $response->getStatusCode(); // 200
		$this->response['reasonPhrase'] = $response->getReasonPhrase(); // OK
		$this->response['protocolVersion'] = $response->getProtocolVersion(); // 1.1
		$this->response['body'] = $response->getBody();
		return $this->response;
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
	public function verifyTxn($rid='',$pid,$amt){
		if($rid == '') {
			$this->response['error'] = true;
			$this->response['success'] = false;
			$this->response['errMessage'] = 'Txn reference ID missing';
			return false;
		}

		$postparams = array(
			'rid'=>$rid,
			'pid'=>$pid,
			'amt'=>$amt,
			'scd'=>$this->scd
		);
		$client = new Client();
		$response = $client->request('POST', $this->vu, [
		    'form_params' => $postparams
		]);

		$this->response['statusCode'] = $response->getStatusCode(); // 200
		$this->response['reasonPhrase'] = $response->getReasonPhrase(); // OK
		$this->response['protocolVersion'] = $response->getProtocolVersion(); // 1.1
		$this->response['body'] = $response->getBody();
		return $this->response;
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