1.4.0, 2024-03-07:
- [smartform] Smartform integration.
- Set return mode to POST by default.
- Update list of supported payment means.
- Update list of supported currencies.

1.3.4, 2022-05-05:
- Update list of supported payment means.

1.3.3, 2021-06-28:
- Do not use vads\_order\_info, use vads\_ext\_info\_* instead.
- Send the relevant part of the current PHP version in vads\_contrib field.
- Improve support e-mail display.
- Improve payment in installments options display on osCommerce backend.
- Update 3DS management option description.

1.3.2, 2020-06-19:
- Fix some plugin translations.

1.3.1, 2019-02-28:
- Added Spanish translations.
- Improve plugin translations (rename certifcate to key, namely refer to Back Office, others fixes).
- Do not display error message when payment is cancelled by the buyer.

1.3.0, 2018-10-15:
- Bug fix: signature error with quote in return data.
- Enable signature algorithm selection (SHA-1 or HMAC-SHA-256).
- Improve some plugin translations.
- [prodfaq] Fix notice about shifting the shop to production mode.
- [technical] Manage enabled/disabled features by plugin variant.

1.2.0, 2017-09-05:
- [systempay] Added Choozeo submodule.
- Send cellular phone number in payment form.
- Improve module translations.

1.1.4, 2017-01-16:
- Correction of a problem relative to cart empty after a sucessful payment.
- Refactoring of API code.
- Remove control over certificate format modified on the gateway.

1.1.3, 2015-03-09:
- Bug fix: relative to MySQLi PHP extension.
- Correction of a problem relative to labels and descriptions display in module backend.

1.1b, 2014-06-23:
- Bug fix: module disabled if order currency is not defined.
- Added German translations.
- Added Russian as supported language in payment page.

1.1, 2013-11-25:
- Integration of the payment in installments.
- Added selective 3-DS option.
- Added amount restriction to enable module.

1.0b, 2012-05-24:
- Handling two payments having the same order number (osCommerce bug).

1.0, 2011-09-12:
- Initial version.
