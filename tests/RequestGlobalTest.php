<?php

namespace PlugRoute\Test;

use PHPUnit\Framework\TestCase;
use PlugHttp\Body\Content;
use PlugHttp\Globals\GlobalGet;
use PlugHttp\Globals\GlobalFile;
use PlugHttp\Globals\GlobalRequest;
use PlugRoute\Test\Classes\ServerClassUrlEncoded;

class RequestGlobalTest extends TestCase
{
	private $instance;

	public function setUp()
	{
		$get = new GlobalGet(['query' => true, 'start' => 10, 'limit' => 30]);
		$server = new ServerClassUrlEncoded(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/test']);
		$server->flag(2);
		$content = new Content($server);
		$fileArray = [
			'myFile' => [
				'name' => '',
				'type' => 'application/pdf',
				'tmp_name' => '/tmp/phpTvssJ2',
				'error' => 0,
				'size' => 1118907,
			]
		];
		$file = new GlobalFile($fileArray);
		$this->instance = new GlobalRequest($content->getBody(), $get, $file, $server);
	}

	public function testAll()
	{
		self::assertEquals(['test' => 'myTest', 'lang' => 'PHP', 'dev' => 'Erandir'], $this->instance->all());
	}

	public function testInput()
	{
		self::assertEquals('myTest', $this->instance->input('test'));
	}

	public function testExcept()
	{
		self::assertEquals(['lang' => 'PHP', 'dev' => 'Erandir'], $this->instance->except(['test']));
	}

	public function testOnly()
	{
		$expected = ['test' => 'myTest', 'dev' => 'Erandir'];
		self::assertEquals($expected, $this->instance->only(['test', 'dev']));
	}

	public function testQuery()
	{
		$expected = ['query' => true, 'start' => 10, 'limit' => 30];
		self::assertEquals($expected, $this->instance->query());
	}

	public function testQueryWith()
	{
		self::assertEquals(30, $this->instance->queryWith('limit'));
	}

	public function testQueryOnly()
	{
		$expected = ['start' => 10];
		self::assertEquals($expected, $this->instance->queryOnly(['start']));
	}

	public function testQueryExcept()
	{
		$expected = ['query' => true, 'start' => 10];
		self::assertEquals($expected, $this->instance->queryExcept(['limit']));
	}

	public function testHas()
	{
		self::assertEquals(true, $this->instance->has('dev'));
	}

	public function testMethod()
	{
		self::assertEquals('POST', $this->instance->method());
	}

	public function testIsMethod()
	{
		self::assertEquals(true, $this->instance->isMethod('POST'));
	}

	public function testUrl()
	{
		self::assertEquals('/test', $this->instance->getUrl());
	}

	public function testRemove()
	{
		$expected = ['test' => 'myTest', 'dev' => 'Erandir'];
		self::assertEquals($expected, $this->instance->remove('lang')->all());
	}

	public function testAdd()
	{
		$expected = ['test' => 'myTest', 'lang' => 'PHP', 'dev' => 'Erandir', 'newValue' => true];
		self::assertEquals($expected, $this->instance->add('newValue', true)->all());
	}

	public function testRemoveQuery()
	{
		$expected = ['query' => true, 'limit' => 30];
		self::assertEquals($expected, $this->instance->removeQuery('start')->query());
	}

	public function testAddQuery()
	{
		$expected = ['query' => true, 'start' => 10, 'limit' => 30, 'new' => 'queryValue'];
		$result = $this->instance->addQuery('new', 'queryValue')->query();
		self::assertEquals($expected, $result);
	}

	public function testFile()
	{
		$expected = [
			'myFile' => [
				'name' => '',
				'type' => 'application/pdf',
				'tmp_name' => '/tmp/phpTvssJ2',
				'error' => 0,
				'size' => 1118907,
			]
		];

		$result = $this->instance->files();
		self::assertEquals($expected, $result);
	}

	/**
	 * @testQueryExcept
	 * @runInSeparateProcess
	 **/
	public function testRedirect()
	{
		$this->instance->redirect('https://github.com/');

		$this->assertContains(
			'Location: https://github.com/', xdebug_get_headers()
		);
	}
}