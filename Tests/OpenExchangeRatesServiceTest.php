<?php
namespace Mrzard\OpenExchangeRatesBundle\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use Mrzard\OpenExchangeRates\Service\OpenExchangeRatesService;

class OpenExchangeRatesServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OpenExchangeRatesService
     */
    protected $mockedService;

    /**
     * Get service configuration
     *
     * @return array
     */
    protected function getServiceConfig()
    {
        return array(
            'base_currency' => 'USD',
            'https' => false
        );
    }

    /**
     * Set up test
     */
    public function setUp()
    {
        $fakeResponse = $this
            ->getMockBuilder('GuzzleHttp\Message\Response')
            ->setMethods(array('json'))
            ->setConstructorArgs(array('200'))
            ->getMock();

        $fakeResponse
            ->expects($this->any())
            ->method('json')
            ->will($this->returnValue(array('ok' => true)));

        $fakeClient = $this
            ->getMockBuilder('GuzzleHttp\Client')
            ->setMethods(array('send'))
            ->getMock();

        //our client will always return a our request
        $fakeClient
            ->expects($this->any())
            ->method('send')
            ->withAnyParameters()
            ->will($this->returnValue($fakeResponse));

        $this->mockedService = $this
            ->getMockBuilder(
                'Mrzard\OpenExchangeRates\Service\OpenExchangeRatesService'
            )
            ->setConstructorArgs(array('f4k3', $this->getServiceConfig(), $fakeClient))
            ->setMethods(null)
            ->getMock();

    }

    /**
     * Test that the functions can run
     */
    public function testService()
    {
        $latest = $this->mockedService->getLatest(array(), null);
        $this->assertTrue(
            $latest['ok'], 'getLatest failed'
        );
        $latest = $this->mockedService->getLatest(array('EUR'), null);
        $this->assertTrue(
            $latest['ok'], 'getLatest failed'
        );
        $latest = $this->mockedService->getLatest(array('EUR'), 'USD');
        $this->assertTrue(
            $latest['ok'], 'getLatest failed'
        );
        $latest = $this->mockedService->getLatest(array(), 'USD');
        $this->assertTrue(
            $latest['ok'], 'getLatest failed'
        );
        $currencies = $this->mockedService->getCurrencies();
        $this->assertTrue(
            $currencies['ok'], 'getCurrencies failed'
        );
        $convertCurrency = $this->mockedService->convertCurrency(10, 'EUR', 'USD');
        $this->assertTrue(
            $convertCurrency['ok'],
            'convertCurrency failed'
        );
        $getHistorical =$this->mockedService->getHistorical(new \DateTime('2014-01-01'));
        $this->assertTrue(
            $getHistorical['ok'],
            'getHistorical failed'
        );
    }

    /**
     * Test that the class can be instantiated
     */
    public function testInstantiation()
    {
        $service = new OpenExchangeRatesService(
            'f4k31d',
            $this->getServiceConfig(),
            new Client()
        );
        $this->assertTrue($service instanceof OpenExchangeRatesService, 'Creation failed');
    }

    /**
     * Test what happens when an error is thrown
     */
    public function testError()
    {
        $appId = 'f4k31d';
        $fakeRequest = $this
            ->getMockBuilder('GuzzleHttp\Message\Request')
            ->setConstructorArgs(array(
                'GET',
                'localhost',
                array()
            ))
            ->setMethods(array('send', 'getResponse'))
            ->getMock();

        //make send throw an exception
        $fakeRequest->expects($this->any())->method('send')->willThrowException(
            new \Exception('testException')
        );

        //all request will return a fake response
        $fakeResponse = $this
            ->getMockBuilder('GuzzleHttp\Message\Response')
            ->setMethods(array('json'))
            ->setConstructorArgs(array('200', array(), Stream::factory(json_encode(array('ok' => true)))))
            ->getMock();

        $fakeRequest
            ->expects($this->any())
            ->method('getResponse')
            ->willReturn($fakeResponse);

        //create our fake client
        $fakeClient = $this
            ->getMockBuilder('GuzzleHttp\Client')
            ->setMethods(array('createRequest'))
            ->getMock();

        //our client will always return a our request
        $fakeClient
            ->expects($this->any())
            ->method('createRequest')
            ->withAnyParameters()
            ->will($this->returnValue($fakeRequest));


        $this->mockedService = $this
            ->getMockBuilder(
                'Mrzard\OpenExchangeRates\Service\OpenExchangeRatesService'
            )
            ->setConstructorArgs(array($appId, $this->getServiceConfig(), $fakeClient))
            ->setMethods(null)
            ->getMock();

        $this->assertArrayHasKey(
            'error',
            $this->mockedService->getCurrencies(),
            'Error was not properly checked'
        );
    }

    /**
     * Test general config
     */
    public function testConfig()
    {
        $config = $this->getServiceConfig();

        $this->assertEquals(
            $config['https'],
            $this->mockedService->useHttps(),
            'https config mismatch'
        );

        $this->assertEquals(
            $config['base_currency'],
            $this->mockedService->getBaseCurrency(),
            'base_currency config mismatch'
        );

        $this->mockedService->setHttps(true);
        $this->assertEquals(
            true,
            $this->mockedService->useHttps(),
            'https setter failed'
        );
        $this->assertEquals(
            'https://openexchangerates.org/api',
            $this->mockedService->getEndPoint(),
            'Endpoint does not look right'
        );

        $this->mockedService->setHttps(false);
        $this->assertEquals(
            'http://openexchangerates.org/api',
            $this->mockedService->getEndPoint(),
            'Endpoint does not look right'
        );

        $this->mockedService->setBaseCurrency('EUR');
        $this->assertEquals(
            'EUR',
            $this->mockedService->getBaseCurrency(),
            'base currency setter failed'
        );
    }
}