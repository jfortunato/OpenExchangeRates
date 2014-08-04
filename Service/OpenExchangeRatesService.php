<?php

/**
 * OpenExchangeRates Bundle for Symfony2
 *
 * @author Gonzalo Míguez (mrzard@gmail.com)
 * @since 2014
 */

namespace Mrzard\OpenExchangeRates\Service;

use DateTime;
use Exception;
use Guzzle\Http\Client;
use Guzzle\Http\Message\RequestInterface;

/**
 * Class OpenExchangeRatesService
 *
 * This class exposes the OpenExchangeRates API
 *
 * @package Mrzard\OpenExchangeRatesBundle\Service
 */
class OpenExchangeRatesService
{
    /**
     * @var string
     * 
     * the app id
     */
    protected $appId;

    /**
     * @var string
     * 
     * the api endpoint
     */
    protected $endPoint = '://openexchangerates.org/api';

    /**
     * @var string
     * 
     * base currency
     */
    protected $baseCurrency = '';

    /**
     * @var Client
     * 
     * Client
     */
    protected $client;

    /**
     * @var bool
     * 
     * https is used
     */
    protected $https;

    /**
     * Service constructor
     *
     * @param string $openExchangeRatesAppId the app_id for OpenExchangeRates
     * @param array  $apiOptions             Options for the OpenExchangeRatesApi
     * @param Client $client                 Guzzle client for requests
     */
    public function __construct($openExchangeRatesAppId, $apiOptions, Client $client)
    {
        $this->appId = $openExchangeRatesAppId;
        $this->https = (bool) $apiOptions['https'];
        $this->baseCurrency = (string) $apiOptions['base_currency'];
        $this->client = $client;
    }


    /**
     * @return string
     */
    public function getEndPoint()
    {
        return ($this->useHttps() ? 'https' : 'http').$this->endPoint;
    }

    /**
     * Get the appId
     *
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * Get if https is enabled
     *
     * @return boolean
     */
    public function useHttps()
    {
        return $this->https;
    }

    /**
     * Sets whether to use https
     *
     * @param boolean $https
     */
    public function setHttps($https)
    {
        $this->https = (bool) $https;
    }

    /**
     * Get the base currency
     *
     * @return string
     */
    public function getBaseCurrency()
    {
        return $this->baseCurrency;
    }

    /**
     * Set the base currency
     *
     * @param string $baseCurrency
     */
    public function setBaseCurrency($baseCurrency)
    {
        $this->baseCurrency = $baseCurrency;
    }


    /**
     * Converts $value from currency $symbolFrom to currency $symbolTo
     *
     * @param float  $value      value to convert
     * @param string $symbolFrom symbol to convert from
     * @param string $symbolTo   symbol to convert to
     *
     * @return float
     */
    public function convertCurrency($value, $symbolFrom, $symbolTo)
    {
        $query = array('app_id' => $this->getAppId());

        $request = $this->client->createRequest(
            'GET',
            $this->getEndPoint().'/convert/'.$value.'/'.$symbolFrom.'/'.$symbolTo,
            null,
            null,
            array('query' => $query)
        );

        return $this->runRequest($request);
    }

    /**
     * Get the latest exchange rates
     *
     * @param array  $symbols array of currency codes to get the rates for.
     *                        Default empty (all currencies)
     * @param string $base    Base currency, default NULL (gets it from config)
     *
     * @return array
     */
    public function getLatest(array $symbols = array(), $base = null)
    {
        $query = array(
            'app_id' => $this->getAppId(),
            'base' => is_null($base) ? $this->getBaseCurrency() : $base
        );

        if (count($symbols)) {
            $query['symbols'] = implode(',', $symbols);
        }

        $request = $this->client->createRequest(
            'GET',
            $this->getEndPoint().'/latest.json',
            null,
            null,
            array('query' => $query)
        );

        return $this->runRequest($request);
    }


    /**
     * Gets a list of all available currencies
     */
    public function getCurrencies()
    {
        $request = $this->client->createRequest(
            'GET',
            $this->getEndPoint().'/currencies.json',
            null,
            null,
            array('query' => array('app_id' => $this->getAppId()))
        );

        return $this->runRequest($request);
    }


    /**
     * Run guzzle request
     *
     * @param RequestInterface $request
     *
     * @return array
     */
    private function runRequest(RequestInterface $request)
    {
        try {
            $request->send();
            //send the req and return the json
            return $request->getResponse()->json();
        } catch (Exception $e) {
            return array('error' => $request->getResponse()->json());
        }
    }


    /**
     * Get historical data
     *
     * @param \DateTime $date
     *
     * @return array
     */
    public function getHistorical(DateTime $date)
    {
        $request = $this->client->createRequest(
            'GET',
            $this->getEndPoint().'/historical/'.$date->format('Y-m-d').'.json',
            null,
            null,
            array(
                array(
                    'query' =>
                        array(
                            'app_id' => $this->getAppId(),
                            'base' => $this->getBaseCurrency()
                        )
                )
            )
        );

        return $this->runRequest($request);
    }
}