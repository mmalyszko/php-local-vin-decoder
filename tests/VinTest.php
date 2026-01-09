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
    private const VALID_VIN           = '1HGCM82633A004352';
    private const INVALID_VIN         = 'INVALIDVIN1234567';
    private const HONDA_VIN           = '1HGCM82633A004352';
    private const OPEL_VIN            = 'W0L0TGF487G011234';
    private const VALID_2010_VIN      = '1HGCM8263A0043521';
    private const UNKNOWN_BRAND_VIN   = 'ZZZCM82633A004352';
    private const UNKNOWN_REGION_VIN  = 'H9ZCM82633A004352';
    private const UNKNOWN_COUNTRY_VIN = 'Z9ZCM82633A004352';
    private const UNKNOWN_YEAR_VIN    = 'H9ZCM8263ZA004352';
    private const INVALID_VIS_VIN     = 'H9ZCM826300000000';
    private const CANADA_VIN          = '2HGCM82633A004352';
    private const GERMANY_VIN         = 'WVWZZZ1JZXW000001';

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
        $this->assertEquals('3A004352', $vin->getVis());
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
        $this->assertEquals('3A004352', $arr['vis']);
    }

    public function testDecodeBrandKnown()
    {
        $vin = new Vin(self::HONDA_VIN);
        $this->assertEquals('Honda', $vin->decodeBrand());

        $vin = new Vin(self::OPEL_VIN);
        $this->assertEquals('Opel', $vin->decodeBrand());
    }

    public function testDecodeBrandUnknown()
    {
        $vin = new Vin(self::UNKNOWN_BRAND_VIN);
        $this->assertNull($vin->decodeBrand());
    }

    public function testDecodeModelKnown()
    {
        $vin = new Vin(self::OPEL_VIN);
        $this->assertEquals('Astra', $vin->decodeModel());
    }

    public function testDecodeModelUnknown()
    {
        $vin = new Vin(self::UNKNOWN_BRAND_VIN);
        $this->assertNull($vin->decodeModel());

        $vin = new Vin(self::INVALID_VIS_VIN);
        $this->assertNull($vin->decodeModel());
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
        $vin = new Vin(self::OPEL_VIN);
        $vin->setVdsDataFilePath(__DIR__.'/missing_vds_file.json');
        $this->assertEquals('Opel', $vin->decodeBrand());
        $this->expectException(\RuntimeException::class);
        $this->assertEquals('Astra', $vin->decodeModel());
    }
}
