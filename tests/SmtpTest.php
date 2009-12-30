<?php
require_once 'PHPUnit/Framework.php';
require_once 'lib/Smtp.php';

use Mail\Protocol\Smtp;
use Mail\Protocol\SmtpException;

class SmtpTest extends PHPUnit_Framework_TestCase
{
	public function testSmtpTCPConnection()
	{
		$smtp = new Smtp( 'localhost', 25, 'tcp' );
		$this->assertFalse( $smtp->isConnected() );
		$smtp->connect();
		$this->assertTrue( $smtp->isConnected() );
		$this->assertTrue( $smtp->noop() );
		$smtp->close();
	}

	public function testSmtpTLSConnection()
	{
		$smtp = new Smtp( 'localhost', 587, 'tls' );
		$this->assertFalse( $smtp->isConnected() );
		$smtp->connect();
		$this->assertTrue( $smtp->isConnected() );
		$this->assertTrue( $smtp->noop() );
		$smtp->close();
	}

	public function testSmtpSSLConnection()
	{
		$smtp = new Smtp( 'localhost', 465, 'ssl' );
		$this->assertFalse( $smtp->isConnected() );
		$smtp->connect();
		$this->assertTrue( $smtp->isConnected() );
		$this->assertTrue( $smtp->noop() );
		$smtp->close();
	}

	public function testSmtpHeloCommand()
	{
		$smtp = new Smtp( 'localhost', 25, 'tcp' );
		$smtp->connect();
		$this->assertTrue( $smtp->helo( 'localhost' ) );
		$smtp->close();
	}

	public function testSmtpEhloCommand()
	{
		$smtp = new Smtp( 'localhost', 587, 'tls' );
		$smtp->connect();
		$this->assertType( 'array', $smtp->ehlo( 'localhost' ) );
		$smtp->close();
	}

	public function testSmtpAuthPlain()
	{
		$smtp = new Smtp( 'localhost', 587, 'tls' );
		$smtp->connect();
		$smtp->helo( 'localhost' );
		$this->assertTrue( $smtp->authenticate( 'poptest', 'foobar12', 'plain' ) );
		$smtp->close();

		$smtp->connect();
		$smtp->helo( 'localhost' );
		try {
			$smtp->authenticate( 'wrong', 'wrong' );
		}
		catch ( SmtpException $e ) {
			return;
		}
		$smtp->close();
		$this->fail();
	}

	public function testSmtpAuthLogin()
	{
		$smtp = new Smtp( 'localhost', 587, 'tls' );
		$smtp->connect();
		$smtp->helo( 'localhost' );
		$this->assertTrue( $smtp->authenticate( 'poptest', 'foobar12', 'login' ) );
		$smtp->close();

		$smtp->connect();
		$smtp->helo( 'localhost' );
		try {
			$smtp->authenticate( 'wrong', 'wrong' );
		}
		catch ( SmtpException $e ) {
			return;
		}
		$smtp->close();
		$this->fail();
	}

	public function testSmtpMailCommand()
	{
		$smtp = new Smtp( 'localhost', 587, 'tls' );
		$smtp->connect();
		$smtp->helo( 'localhost' );
		$smtp->authenticate( 'poptest', 'foobar12' );
		$this->assertTrue( $smtp->mail( 'poptest' ) );
		$smtp->close();
	}

	public function testSmtpRcptCommand()
	{
		$smtp = new Smtp( 'localhost', 587, 'tls' );
		$smtp->connect();
		$smtp->helo( 'localhost' );
		$smtp->authenticate( 'poptest', 'foobar12' );
		$this->assertTrue( $smtp->mail( 'poptest' ) );
		$this->assertTrue( $smtp->rcpt( 'poptest' ) );
		$smtp->close();
	}

	public function testSmtpDataCommand()
	{
		$data =  "From: Pop Test <poptest>\r\n";
		$data .= "To: Ryan Cavicchioni <ryan>\r\n";
		$data .= "Subject: Test\r\n";
		$data .= "\r\n";

		$smtp = new Smtp( 'localhost', 587, 'tls' );
		$smtp->connect();
		$smtp->helo( 'localhost' );
		$smtp->authenticate( 'poptest', 'foobar12' );
		$smtp->mail( 'poptest' );
		$smtp->rcpt( 'ryan' );
		$this->assertTrue( $smtp->data( $data ) );
		$smtp->close();
	}

	public function testSmtpRsetCommand()
	{
		$smtp = new Smtp( 'localhost', 587, 'tls' );
		$smtp->connect();
		$smtp->helo( 'localhost' );
		$smtp->authenticate( 'poptest', 'foobar12' );
		$smtp->mail( 'poptest' );
		$smtp->rcpt( 'poptest' );
		$this->assertTrue( $smtp->reset() );
		$smtp->close();
	}

	public function testSmtpVrfyCommand()
	{
		$smtp = new Smtp( 'localhost', 25, 'tcp' );
		$smtp->connect();
		$this->assertTrue( $smtp->vrfy( 'poptest' ) );
		$this->assertFalse( $smtp->vrfy( 'wrong' ) );
		$smtp->close();
	}

	public function testSmtpQuitCommand()
	{
		$smtp = new Smtp( 'localhost', 587, 'tls' );
		$smtp->connect();
		$this->assertTrue( $smtp->quit() );
		$smtp->close();
	}

	public function testSmtpNoopCommand()
	{
		$smtp = new Smtp( 'localhost', 587, 'tls' );
		$smtp->connect();
		$this->assertTrue( $smtp->noop() );
		$smtp->close();
	}
}
?>