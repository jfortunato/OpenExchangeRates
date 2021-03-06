OpenExchangeRates Service
==============================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/eac3708b-416c-4fc6-9c50-453d54ff8f91/mini.png)](https://insight.sensiolabs.com/projects/eac3708b-416c-4fc6-9c50-453d54ff8f91)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mrzard/OpenExchangeRates/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mrzard/OpenExchangeRates/?branch=master)
[![Build Status](https://travis-ci.org/mrzard/OpenExchangeRates.svg?branch=master)](https://travis-ci.org/mrzard/OpenExchangeRates)


This service will help you use OpenExchangeRates in your project

## Installation

Use composer to require and install the service

``` bash
$ php composer.phar require mrzard/open-exchange-rates-service ~0.1.1
```

## Configuration

When creating an instance of the service, you will have to provide
the basic data neede for accessing the OpenExchangeRates API

``` php

    use Mrzard\OpenExchangeRates\Service\OpenExchangeRatesService;
    use GuzzleHttp\Client;
    ...

    $apiOptions = array(
        'https' => false,
        'base_currency' => 'USD'
    );

    new OpenExchangeRatesService(
        $openExchangeRatesApiId, // your id from openExchangeRatesApi
        $apiOptions,
        new Client()
    );
    ...
```

If you're using a free version, you won't need to change the `https` or
`base_currency` as they only work for Enterprise/Unlimited accounts

## Usage

## Free features

### Get latest exchange rates

``` php

    /**
     * Get the latest exchange rates
     *
     * @param array  $symbols Currency codes to get the rates for. Default all
     * @param string $base    Base currency, default NULL (gets it from config)
     *
     * @return array
     */
    public function getLatest($symbols = array, $base = null)
    {
    }

```

Only use the `$symbols` and `$base` parameters if you have an Enterprise or
Unlimited plan.

Output:

``` php

    array (size=5)

          'disclaimer' => string 'Exchange rates...'

          'license' => string 'Data sourced from...'

          'timestamp' => int 1395396061

          'base' => string 'USD' (length=3)

          'rates' =>

                array (size=166)

                  'AED' => float 3.672721

                  'AFN' => float 56.747225

                  'ALL' => float 101.7573

                  'AMD' => float 417.366998

                  ...
            )
    )
```

### Get available currencies

``` php

    /**
     * Gets a list of all available currencies
     *
     * @return array with keys = ISO codes, content = Currency Name
     */
    public function getCurrencies()
    {
    }

```

Output:

``` php

    array (size=5)

          'AED' => 'United Arab Emirates Dirham'

          'AFN' => 'Afghan Afghani'

          'ALL' => 'Albanian Lek'

          'AMD' => 'Armenian Dram'

          'ANG' => 'Netherlands Antillean Guilder'

          ...

    )

```


### Get historical data for a date

``` php

    /**
     * Get historical data
     *
     * @param \DateTime $date
     */
    public function getHistorical(\DateTime $date)
    {
    }

```

Output:

``` php

    array (size=5)

        'disclaimer' => string 'Exchange rates...'

        'license' => string 'Data sourced from...'

        'timestamp' => int 1388617200

        'base' => string 'USD' (length=3)

        'rates' =>

            array (size=166)

                'AED' => float 3.672524

                'AFN' => float 56.0846

                'ALL' => float 102.06575

                'AMD' => float 408.448002

                'ANG' => float 1.78902

                'AOA' => float 97.598401

                'ARS' => float 6.51658

                'AUD' => float 1.124795

                'AWG' => float 1.789775

                'AZN' => float 0.7841

                'BAM' => float 1.421715

                'BBD' => int 2
          ...
        )
    )
```

## Developer / Unlimited features

### Get the latest exchange rates, limiting the return array

``` php

    $openExchangeRatesService->getLatest(array('EUR', 'USD', 'COP'));

```

Output:

``` php

    array (size=5)

          'disclaimer' => string 'Exchange rates ...'

          'license' => string 'Data sourced...'

          'timestamp' => int 1395396061

          'base' => string 'USD' (length=3)

          'rates' =>

                array (size=3)

                  'EUR' => ...,

                  'USD' => ...,

                  'COP' => ...

                )

    )
```

You can also change the base currency used to get the latest exchange rates with
the second parameter

### Directly convert a quantity between currencies

``` php

    $openExchangeRatesService->convert(10, 'USD', 'EUR');

```
