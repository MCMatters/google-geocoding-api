<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeocoding\Tests;

use McMatters\GoogleGeocoding\GeoCoder;
use McMatters\GoogleGeocoding\Models\Address;
use PHPUnit\Framework\TestCase;
use const false, null;
use function mb_strpos;

/**
 * Class GeoCoderTest
 *
 * @package McMatters\GoogleGeocoding\Tests
 */
class GeoCoderTest extends TestCase
{
    /**
     * @var GeoCoder
     */
    protected $geoCoder;

    /**
     * GeoCoderTest constructor.
     *
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->geoCoder = new GeoCoder(getenv('GOOGLE_GEOCODING_API_KEY'));
    }

    /**
     * Test method "getByAddress".
     */
    public function testGetByAddress()
    {
        $addresses = $this->geoCoder->getByAddress('Kyiv');
        $this->assertTrue($addresses->isNotEmpty());
        $this->assertCount(1, $addresses);
        $this->assertNotEmpty($addresses->first());
    }

    /**
     * Test method "getByLatLng".
     */
    public function testGetByLatLng()
    {
        $lat = 50.4501;
        $lng = 30.5234;

        $addresses = $this->geoCoder->getByLatLng($lat, $lng);
        $addressesFromString = $this->geoCoder->getByLatLng("{$lat},{$lng}");

        $this->assertTrue($addresses->isNotEmpty());
        $this->assertTrue($addressesFromString->isNotEmpty());

        /** @var Address $address */
        foreach ($addresses as $key => $address) {
            /** @var Address $addressFromString */
            $addressFromString = $addressesFromString->get($key);

            $this->assertNotNull($addressesFromString);
            $this->assertSame(
                $address->getFormatted(),
                $addressFromString->getFormatted()
            );
        }

        /** @var Address $address */
        foreach ($addresses as $address) {
            $formatted = $address->getFormatted();

            $this->assertTrue(
                false !== mb_strpos($formatted, 'Kyiv') ||
                false !== mb_strpos($formatted, 'Kiev') ||
                false !== mb_strpos($formatted, 'Киев') ||
                false !== mb_strpos($formatted, 'Київ') ||
                false !== mb_strpos($formatted, 'Ukraine') ||
                false !== mb_strpos($formatted, 'Украина') ||
                false !== mb_strpos($formatted, 'Україна')
            );
        }
    }
}
