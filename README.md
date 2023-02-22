# Adyen Payment Gateway for Vanilo

[![Tests](https://img.shields.io/github/actions/workflow/status/vanilophp/adyen/tests.yml?branch=master&style=flat-square)](https://github.com/vanilophp/adyen/actions?query=workflow%3Atests)
[![Packagist Stable Version](https://img.shields.io/packagist/v/vanilo/adyen.svg?style=flat-square&label=stable)](https://packagist.org/packages/vanilo/adyen)
[![StyleCI](https://styleci.io/repos/390985000/shield?branch=master)](https://styleci.io/repos/390985000)
[![Packagist downloads](https://img.shields.io/packagist/dt/vanilo/adyen.svg?style=flat-square)](https://packagist.org/packages/vanilo/adyen)
[![MIT Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

This library enables [Adyen Drop-in & Components](https://docs.adyen.com/online-payments/drop-in-web)
integration for [Vanilo Payments](https://vanilo.io/docs/master/payments).

Being a [Concord Module](https://konekt.dev/concord/1.9/modules) it is intended to be used by Laravel Applications.

## Documentation

Refer to the markdown files in the [docs](docs/) folder.

## Known Issues

Adyen expects amounts to be specified in "minor" units (eg. cents: 10 EUR => { amount: 1000, currency: "EUR" }).
Currently the library takes the original amount, multiplies it by 100 and
calls it a day. Certain currencies are not like that, we need an
adjustment layer to handle this.
