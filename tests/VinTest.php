<?php

namespace VinDecoder\Tests;

use PHPUnit\Framework\TestCase;
use VinDecoder\Vin;

/**
 * @internal
 *
 * @coversNothing
 */
class VinTest extends TestCase
{
    private const VALID_VIN = '1HGCM826330000000'; // Valid example VIN for Honda Accord

    private const INVALID_VIN     = 'INVALIDVIN0000000'; // 'I' is outside VIN charset => fails regex
    private const INVALID_VIS_VIN = 'H9ZCM826300000000'; // '0' @pos10 => NOT valid

    private const HONDA_BRAND_VIN = '1HGCM826330000000'; // Valid WMI=>brand table entry
    private const OPEL_BRAND_VIN  = 'W0L0TGF4870000000'; // Valid WMI=>brand table entry

    private const VALID_2010_VIN = '1HGCM8263A0000000'; // 'A' @pos10 => 2010 (in y2026 2nd 30y cycle, not 1980)

    private const UNKNOWN_BRAND_VIN   = 'ZZZCM826330000000'; // WMI 'ZZZ' => not in WMI table
    private const UNKNOWN_REGION_VIN  = 'H9ZCM826330000000'; // WMI starts 'H' => not in REGIONS table
    private const UNKNOWN_COUNTRY_VIN = 'Z9ZCM826330000000'; // 'Z'=Europe => known, but 2nd char '9' matches no country
    private const UNKNOWN_YEAR_VIN    = 'H9ZCM8263Z0000000'; // Z @10 => NOT valid

    private const CANADA_VIN  = '2HGCM826330000000'; // WMI starts '2' => Canada
    private const GERMANY_VIN = 'WVWZZZ1JZX0000000'; // WMI starts 'W' => Germany

    private const BRAND_WMI2CHARS_VIN = 'JHMCV1F16N0000000'; // Example JH* 2-chars pattern for Honda

    public function testNewVinValid()
    {
        $vin = new Vin(self::VALID_VIN);
        $this->assertInstanceOf(Vin::class, $vin);
    }

    public function testNewVinInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Vin(self::INVALID_VIN);
    }

    public function testGetVinWmiVds()
    {
        $vin = new Vin(self::VALID_VIN);
        $this->assertEquals(self::VALID_VIN, $vin->getVin());
        $this->assertEquals('1HG', $vin->getWmi());
        $this->assertEquals('CM8263', $vin->getVds());
        $this->assertEquals('30000000', $vin->getVis());
    }

    public function testToString()
    {
        $vin = new Vin(self::VALID_VIN);
        $this->assertEquals(self::VALID_VIN, (string) $vin);
    }

    public function testToArray()
    {
        $vin = new Vin(self::VALID_VIN);
        $arr = $vin->toArray();
        $this->assertEquals(self::VALID_VIN, $arr['vin']);
        $this->assertEquals('1HG', $arr['wmi']);
        $this->assertEquals('CM8263', $arr['vds']);
        $this->assertEquals('30000000', $arr['vis']);
    }

    public function testDecodeBrandKnown()
    {
        $vin = new Vin(self::HONDA_BRAND_VIN);
        $this->assertEquals('Honda', $vin->decodeBrand());

        $vin = new Vin(self::OPEL_BRAND_VIN);
        $this->assertEquals('Opel', $vin->decodeBrand());
    }

    public function testDecodeBrandUnknown()
    {
        $vin = new Vin(self::UNKNOWN_BRAND_VIN);
        $this->assertNull($vin->decodeBrand());
    }

    public function testDecodeModelKnown()
    {
        $vin = new Vin(self::HONDA_BRAND_VIN);
        $this->assertEquals('Accord', $vin->decodeModel());
    }

    public function testDecodeModelUnknown()
    {
        $vin = new Vin(self::UNKNOWN_BRAND_VIN);
        $this->assertNull($vin->decodeModel());

        $vin = new Vin(self::INVALID_VIS_VIN);
        $this->assertNull($vin->decodeModel());
    }

    public function testDecodeModelUsing2CharsWmi(): void
    {
        $vin = new Vin(self::BRAND_WMI2CHARS_VIN);
        $this->assertSame('JHM', $vin->getWmi());
        $this->assertSame('CV1F16', $vin->getVds());
        $this->assertSame('Honda', $vin->decodeBrand());
        $this->assertSame('Accord', $vin->decodeModel());
    }

    public function testDecodeYearValid()
    {
        $vin = new Vin(self::VALID_VIN);
        $this->assertEquals(2003, $vin->decodeYear());

        $vin = new Vin(self::VALID_2010_VIN);
        $this->assertEquals(2010, $vin->decodeYear());
    }

    public function testDecodeYearInvalid()
    {
        $vin = new Vin(self::UNKNOWN_YEAR_VIN);
        $this->assertNull($vin->decodeYear());

        $vin = new Vin(self::INVALID_VIS_VIN);
        $this->assertNull($vin->decodeYear());
    }

    public function testDecodeRegionKnown()
    {
        $vin = new Vin(self::VALID_VIN);
        $this->assertEquals('North America', $vin->decodeRegion());

        $vin = new Vin(self::GERMANY_VIN);
        $this->assertEquals('Europe', $vin->decodeRegion());
    }

    public function testDecodeRegionUnknown()
    {
        $vin = new Vin(self::UNKNOWN_REGION_VIN);
        $this->assertNull($vin->decodeRegion());
    }

    public function testDecodeCountryKnown()
    {
        $vin = new Vin(self::VALID_VIN);
        $this->assertEquals('USA', $vin->decodeCountry());

        $vin = new Vin(self::CANADA_VIN);
        $this->assertEquals('Canada', $vin->decodeCountry());
    }

    public function testDecodeCountryUnknown()
    {
        $vin = new Vin(self::UNKNOWN_COUNTRY_VIN);
        $this->assertNull($vin->decodeCountry());

        $vin = new Vin(self::UNKNOWN_REGION_VIN);
        $this->assertNull($vin->decodeCountry());
    }

    public function testMissingVdsFile()
    {
        $vin = new Vin(self::OPEL_BRAND_VIN);
        $vin->setVdsDataFilePath(__DIR__.'/missing_vds_file.json');
        $this->assertEquals('Opel', $vin->decodeBrand());
        $this->expectException(\RuntimeException::class);
        $vin->decodeModel();
    }
}
