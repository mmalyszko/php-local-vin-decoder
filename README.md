# Lightweight VIN Decoder for PHP 8+

[![Tests](https://github.com/mmalyszko/php-local-vin-decoder/actions/workflows/tests.yml/badge.svg)](https://github.com/mmalyszko/php-local-vin-decoder/actions/workflows/tests.yml)

Decodes a vehicle's brand, model, year, country and region from its VIN, locally and without external API calls.

Originally created as an educational example of building and publishing a PHP library, but it might be useful to someone, so I'll keep it around.

## Usage

```php
use VinDecoder\Vin;

$vin = new Vin('1HGCM826330000000');

echo $vin->decodeBrand();   // Honda
echo $vin->decodeModel();   // Accord (works if mapping is present in VDS file)
echo $vin->decodeYear();    // 2003
echo $vin->decodeCountry(); // USA
echo $vin->decodeRegion();  // North America

```

## VDS file

Default mapping is a limited example based on public NHTSA data - you can override it with your own file.

```php
$vin->setVdsDataFilePath('/path/to/your/vds.php');
```

## Testing

| Test         | Command                 |
|--------------|-------------------------|
| PHPUnit      | `composer test`         |
| Coverage     | `composer test:cover`   |
| PHP CS Fixer | `composer format:check` |
| PHPStan      | `composer lint`         |
