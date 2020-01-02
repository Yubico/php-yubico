<?php

require_once(__DIR__ . '/../Yubico.php');


class YubicoTest extends \PHPUnit\Framework\TestCase {
  private $yubi;

  public function setUp(): void {
    $this->yubi = new Auth_Yubico(27655, '9Tt8Gg51VG/dthDKgopt0n8IXVI=');
    error_reporting(E_WARNING);
  }

  public function testVerify() {
    $otp = 'vvincrediblegfnchniugtdcbrleehenethrlbihdijv';
    $ret = $this->yubi->verify($otp);
    $this->assertEquals('REPLAYED_OTP', $ret);
  }

  public function testBadOTP() {
    $otp = 'vvincrediblegfnchniugtdcbrleehenethrlbihdijc';
    $ret = $this->yubi->verify($otp);
    $this->assertEquals('NO_VALID_ANSWER', $ret);
  }
}

class RetryTest extends \PHPUnit\Framework\TestCase {
  private $yubi;
  private $webserver_url;
  private $webserver_pid;
    
  public function setUp(): void {
    $this->yubi = new Auth_Yubico(27655, '9Tt8Gg51VG/dthDKgopt0n8IXVI=');
    $this->webserver_url = "http://localhost:3961";

    $this->yubi->setURLpart($this->webserver_url . "/tests/mock_verify.php");
	error_reporting(E_WARNING);
  }

  function setup_mock($response_code, $response_body) {
	/* mock_verify will take requests like
	 * /tests/mock_verify.php/500/status=NOPE?id=... and return a 500
	 * with a body of status=NOPE
	 */
	$this->yubi->setURLpart($this->webserver_url . "/tests/mock_verify.php/" . $response_code . "/" . $response_body);
  }
    
  public function testRetry500() {
	$otp = 'vvincrediblegfnchniugtdcbrleehenethrlbihdijv';
	$this->setup_mock(500, "status=OOPS");

	$ret = $this->yubi->verify($otp);
	$this->assertEquals('NO_VALID_ANSWER', $ret);
	$this->assertEquals(3, $this->yubi->getRetries());
  }

  public function testRetry400() {
	$otp = 'vvincrediblegfnchniugtdcbrleehenethrlbihdijv';
	$this->setup_mock(400, "status=OK");

	$ret = $this->yubi->verify($otp);
	$this->assertEquals('NO_VALID_ANSWER', $ret);
	$this->assertEquals(3, $this->yubi->getRetries());
  }
}
