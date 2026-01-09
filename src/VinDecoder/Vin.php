<?php

namespace VinDecoder;

/**
 * Lightweight VIN decoder for PHP 8+.
 *
 * Quickly decode vehicle brand, model, year, country, and region from a VIN — 100% local, no external API required.
 */
class Vin
{
    private const REGEX = '/^[A-HJ-NPR-Z0-9]{17}$/';

    private const WMI = [
        'WAU' => 'Audi',
        'WA1' => 'Audi',
        'TRU' => 'Audi',
        'WBA' => 'BMW',
        'WBY' => 'BMW',
        'WBS' => 'BMW',
        'WBW' => 'BMW',
        'SCB' => 'Bentley',
        'LSY' => 'Brilliance',
        '1G4' => 'Buick',
        '1G6' => 'Cadillac',
        '1GY' => 'Cadillac',
        '1G1' => 'Chevrolet',
        '2G1' => 'Chevrolet',
        '1GC' => 'Chevrolet',
        '2A4' => 'Chrysler',
        '1C3' => 'Chrysler',
        '2C3' => 'Chrysler',
        '1C4' => 'Chrysler',
        'VF7' => 'Citroen',
        'VS7' => 'Citroen',
        'KL'  => 'Daewoo',
        'SUP' => 'Daewoo',
        'JD'  => 'Daihatsu',
        '1D3' => 'Dodge',
        '1B3' => 'Dodge',
        '3D4' => 'Dodge',
        '2B3' => 'Dodge',
        'ZFF' => 'Ferrari',
        'SUF' => 'Fiat',
        'ZFA' => 'Fiat',
        '1FA' => 'Ford',
        'WF0' => 'Ford',
        '1ZV' => 'Ford',
        '3FA' => 'Ford',
        '2FM' => 'Ford',
        '6FP' => 'Ford',
        '1FM' => 'Ford',
        '2FA' => 'Ford',
        '1FT' => 'Ford',
        'MAJ' => 'Ford',
        'VS6' => 'Ford',
        'NM0' => 'Ford',
        '1GT' => 'GMC',
        '5J'  => 'Honda',
        '2HG' => 'Honda',
        'SHS' => 'Honda',
        'JH'  => 'Honda',
        '1H'  => 'Honda',
        'SHH' => 'Honda',
        'NLA' => 'Honda',
        '3H'  => 'Honda',
        '5F'  => "Honda",
        'MRH' => 'Honda',
        '2HK' => 'Honda',
        'KM'  => 'Hyundai',
        'TMA' => 'Hyundai',
        'NLH' => 'Hyundai',
        'MAL' => 'Hyundai',
        '5NP' => 'Hyundai',
        'JA'  => 'Isuzu',
        'MPA' => 'Isuzu',
        'SAJ' => 'Jaguar',
        'SAD' => 'Jaguar',
        '1J8' => 'Jeep',
        '1J4' => 'Jeep',
        'KN'  => 'Kia',
        'U5Y' => 'Kia',
        'U6Y' => 'Kia',
        'ZHW' => 'Lamborghini',
        'ZLA' => 'Lancia',
        'SAL' => 'Land Rover',
        '1L'  => 'Lincoln',
        '5L'  => 'Lincoln',
        'ZAM' => 'Maserati',
        'JMZ' => 'Mazda',
        'JM1' => 'Mazda',
        '3MZ' => 'Mazda',
        '3MD' => 'Mazda',
        '4F'  => 'Mazda',
        '1YV' => 'Mazda',
        'YCM' => 'Mazda',
        'W1K' => 'Mercedes',
        'WDD' => 'Mercedes',
        'WDC' => 'Mercedes',
        'WDF' => 'Mercedes',
        'WDB' => 'Mercedes',
        'W1V' => 'Mercedes',
        '4JG' => 'Mercedes',
        'W1N' => 'Mercedes',
        'VSA' => 'Mercedes',
        '4M'  => 'Mercury',
        '2M'  => 'Mercury',
        'WMW' => 'Mini',
        'MMC' => 'Mitsubishi',
        'JA3' => 'Mitsubishi',
        'JMB' => 'Mitsubishi',
        'JA4' => 'Mitsubishi',
        'XMC' => 'Mitsubishi',
        'MMB' => 'Mitsubishi',
        '6MM' => 'Mitsubishi',
        'JMY' => 'Mitsubishi',
        'SJN' => 'Nissan',
        'JN'  => 'Nissan',
        'VSK' => 'Nissan',
        'MDH' => 'Nissan',
        '1N'  => 'Nissan',
        '5N1' => 'Nissan',
        '3N'  => 'Nissan',
        'VNV' => 'Nissan',
        'W0L' => 'Opel',
        'W0V' => 'Opel',
        'VF3' => 'Peugeot',
        '1GM' => 'Pontiac',
        '1G2' => 'Pontiac',
        'WP1' => 'Porsche',
        'WP0' => 'Porsche',
        'VF1' => 'Renault',
        'VF6' => 'Renault',
        'UU1' => 'Renault',
        'SAR' => 'Rover',
        'YS3' => 'Saab',
        'VSS' => 'Seat',
        'TMB' => 'Skoda',
        'WME' => 'Smart',
        'KPT' => 'SsangYong',
        'KPA' => 'SsangYong',
        'JF'  => 'Subaru',
        'TSM' => 'Suzuki',
        'JS'  => 'Suzuki',
        'VSE' => 'Suzuki',
        'MA3' => 'Suzuki',
        'MMS' => 'Suzuki',
        'MAT' => 'Tata',
        '5YJ' => 'Tesla',
        'LRW' => 'Tesla',
        'YAR' => 'Toyota',
        'JT'  => 'Toyota',
        'SB1' => 'Toyota',
        'AHT' => 'Toyota',
        'NMT' => 'Toyota',
        'VNK' => 'Toyota',
        '5T'  => 'Toyota',
        '4T'  => 'Toyota',
        '2T'  => 'Toyota',
        'MR0' => 'Toyota',
        'VXK' => 'Vauxhall',
        'VXE' => 'Vauxhall',
        'WVW' => 'Volkswagen',
        'WVG' => 'Volkswagen',
        'WV1' => 'Volkswagen',
        'WV2' => 'Volkswagen',
        '3VW' => 'Volkswagen',
        'VWV' => 'Volkswagen',
        '1VW' => 'Volkswagen',
        'WV3' => 'Volkswagen',
        'YV1' => 'Volvo',
        'YV4' => 'Volvo',
        '7JR' => 'Volvo',
        'XLB' => 'Volvo',
        'LVY' => 'Volvo',
    ];

    private const REGIONS = [
        'A' => ['Africa', [
            'ABCDEFGH' => 'South Africa',
            'JKLMN'    => 'Ivory Coast',
        ]],
        'B' => ['Africa', [
            'ABCDE' => 'Angola',
            'FGHJK' => 'Kenya',
            'LMNPR' => 'Tanzania',
        ]],
        'C' => ['Africa', [
            'ABCDE' => 'Benin',
            'FGHJK' => 'Madagascar',
            'LMNPR' => 'Tunisia',
        ]],
        'D' => ['Africa', [
            'ABCDE' => 'Egypt',
            'FGHJK' => 'Morocco',
            'LMNPR' => 'Zambia',
        ]],
        'E' => ['Africa', [
            'ABCDE' => 'Ethiopia',
            'FGHJK' => 'Mozambique',
        ]],
        'F' => ['Africa', [
            'ABCDE' => 'Ghana',
            'FGHJK' => 'Nigeria',
        ]],
        'J' => ['Asia', [
            '*' => 'Japan', // * for ABCDEFGHJKLMNPRSTUVWXYZ1234567890 to skip str_contains
        ]],
        'K' => ['Asia', [
            'ABCDE'              => 'Sri Lanka',
            'FGHJK'              => 'Israel',
            'LMNPR'              => 'South Korea',
            'STUVWXYZ1234567890' => 'Kazakhstan',
        ]],
        'L' => ['Asia', [
            '*' => 'China',
        ]],
        'M' => ['Asia', [
            'ABCDE'              => 'India',
            'FGHJK'              => 'Indonesia',
            'LMNPR'              => 'Thailand',
            'STUVWXYZ1234567890' => 'Myanmar',
        ]],
        'N' => ['Asia', [
            'ABCDE' => 'Iran',
            'FGHJK' => 'Pakistan',
            'LMNPR' => 'Turkey',
        ]],
        'P' => ['Asia', [
            'ABCDE' => 'Philippines',
            'FGHJK' => 'Singapore',
            'LMNPR' => 'Malaysia',
        ]],
        'R' => ['Asia', [
            'ABCDE'              => 'United Arab Emirates',
            'FGHJK'              => 'Taiwan',
            'LMNPR'              => 'Vietnam',
            'STUVWXYZ1234567890' => 'Saudi Arabia',
        ]],
        'S' => ['Europe', [
            'ABCDEFGHJKLM' => 'United Kingdom',
            'NPRST'        => 'East Germany',
            'UVWXYZ'       => 'Poland',
            '1234'         => 'Latvia',
        ]],
        'T' => ['Europe', [
            'ABCDEFGH' => 'Switzerland',
            'JKLMNP'   => 'Czech Republic',
            'RSTUV'    => 'Hungary',
            'WXYZ1'    => 'Portugal',
        ]],
        'U' => ['Europe', [
            'HJKLM'  => 'Denmark',
            'NPRST'  => 'Ireland',
            'UVWXYZ' => 'Romania',
            '567'    => 'Slovakia',
        ]],
        'V' => ['Europe', [
            'ABCDE'      => 'Austria',
            'FGHJKLMNPR' => 'France',
            'STUVW'      => 'Spain',
            'XYZ12'      => 'Serbia',
            '345'        => 'Croatia',
            '67890'      => 'Estonia',
        ]],
        'W' => ['Europe', [
            '*' => 'Germany',
        ]],
        'X' => ['Europe', [
            'ABCDE'    => 'Bulgaria',
            'FGHJK'    => 'Greece',
            'LMNPR'    => 'Netherlands',
            'STUVW'    => 'Russia (USSR)',
            'XYZ12'    => 'Luxembourg',
            '34567890' => 'Russia',
        ]],
        'Y' => ['Europe', [
            'ABCDE' => 'Belgium',
            'FGHJK' => 'Finland',
            'LMNPR' => 'Malta',
            'STUVW' => 'Sweden',
            'XYZ12' => 'Norway',
            '345'   => 'Belarus',
            '67890' => 'Ukraine',
        ]],
        'Z' => ['Europe', [
            'ABCDEFGHJKLMNPR' => 'Italy',
            'XYZ12'           => 'Slovenia',
            '345'             => 'Lithuania',
        ]],
        '1' => ['North America', [
            '*' => 'USA',
        ]],
        '2' => ['North America', [
            '*' => 'Canada',
        ]],
        '3' => ['North America', [
            'ABCDEFGHJKLMNPRSTUVW' => 'Mexico',
            'XYZ1234567'           => 'Costa Rica',
            '890'                  => 'Cayman Islands',
        ]],
        '4' => ['North America', [
            '*' => 'USA',
        ]],
        '5' => ['North America', [
            '*' => 'USA',
        ]],
        '6' => ['Oceania', [
            'ABCDEFGHJKLMNPRSTUVW' => 'Australia',
        ]],
        '7' => ['Oceania', [
            'ABCDE' => 'New Zealand',
        ]],
        '8' => ['South America', [
            'ABCDE' => 'Argentina',
            'FGHJK' => 'Chile',
            'LMNPR' => 'Ecuador',
            'STUVW' => 'Peru',
            'XYZ12' => 'Venezuela',
        ]],
        '9' => ['South America', [
            'ABCDE'   => 'Brazil',
            'FGHJK'   => 'Colombia',
            'LMNPR'   => 'Paraguay',
            'STUVW'   => 'Uruguay',
            'XYZ12'   => 'Trinidad & Tobago',
            '3456789' => 'Brazil',
        ]],
    ];

    private const YEARS = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N',
        'P', 'R', 'S', 'T', 'V', 'W', 'X', 'Y',
        '1', '2', '3', '4', '5', '6', '7', '8', '9',
    ];

    private static ?array $vmiCached = null;
    private string $vdsDataFilePath  = __DIR__.'/data/vds.php';

    private string $vin;
    private string $wmi;
    private string $vds;
    private string $vis;

    public function __construct(string $value)
    {
        $value = strtoupper($value);

        if (!preg_match(self::REGEX, $value)) {
            throw new \InvalidArgumentException("Invalid VIN: must be 17 valid chars (excluding I, O, Q).");
        }

        $this->vin = $value;
        $this->wmi = substr($value, 0, 3);
        $this->vds = substr($value, 3, 6);
        $this->vis = substr($value, 9, 8);
    }

    public function __toString(): string
    {
        return $this->vin;
    }

    public function toArray(): array
    {
        return [
            'vin' => $this->getVin(),
            'wmi' => $this->getWmi(),
            'vds' => $this->getVds(),
            'vis' => $this->getVis(),
        ];
    }

    public function getVin(): string
    {
        return $this->vin;
    }

    public function getWmi(): string
    {
        return $this->wmi;
    }

    public function getVds(): string
    {
        return $this->vds;
    }

    public function getVis(): string
    {
        return $this->vis;
    }

    public function decodeBrand(): ?string
    {
        if (isset(self::WMI[$this->wmi])) {
            return self::WMI[$this->wmi];
        }

        $wmi2 = substr($this->wmi, 0, 2);

        return self::WMI[$wmi2] ?? null;
    }

    public function setVdsDataFilePath(string $path): void
    {
        $this->vdsDataFilePath = $path;
        self::$vmiCached       = null;
    }

    public function decodeModel(): ?string
    {
        if (null === self::$vmiCached) {
            if (!file_exists($this->vdsDataFilePath)) {
                throw new \RuntimeException("VDS data file not found: {$this->vdsDataFilePath}");
            }
            self::$vmiCached = require $this->vdsDataFilePath;
        }

        return self::$vmiCached[$this->wmi][$this->vds] ?? null;
    }

    public function decodeYear(): ?int
    {
        $code = $this->vis[0];

        $index = array_search(strtoupper($code), self::YEARS, true);

        if (false === $index) {
            return null;
        }

        $currentYear       = (int) date('Y');
        $firstPossibleYear = 1980 + $index;

        while ($firstPossibleYear + 30 <= $currentYear + 1) {
            $firstPossibleYear += 30;
        }

        return $firstPossibleYear;
    }

    public function decodeRegion(): ?string
    {
        return self::REGIONS[$this->wmi[0]][0] ?? null;
    }

    public function decodeCountry(): ?string
    {
        $first  = $this->wmi[0] ?? null;
        $second = $this->wmi[1] ?? null;

        if (null === $first || null === $second || !isset(self::REGIONS[$first])) {
            return null;
        }

        $countries = self::REGIONS[$first][1];

        foreach ($countries as $charGroup => $countryName) {
            if ('*' === $charGroup || str_contains($charGroup, $second)) {
                return $countryName;
            }
        }

        return null;
    }
}
