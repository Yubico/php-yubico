<?php

require_once(__DIR__ . '/../Yubico.php');


class YubicoTest extends \PHPUnit\Framework\TestCase {
  private $yubi;

  public function setUp() {
    $this->yubi = new Auth_Yubico(27655, '9Tt8Gg51VG/dthDKgopt0n8IXVI=');
    error_reporting(E_WARNING);
  }

  public function testVerify() {
    $otp = 'vvincrediblegfnchniugtdcbrleehenethrlbihdijv';
    $ret = $this->yubi->verify($otp);
    $this->assertEquals($ret, 'REPLAYED_OTP');
  }

  public function testBadOTP() {
    $otp = 'vvincrediblegfnchniugtdcbrleehenethrlbihdijc';
    $ret = $this->yubi->verify($otp);
    $this->assertEquals($ret, 'NO_VALID_ANSWER');
  }
}
