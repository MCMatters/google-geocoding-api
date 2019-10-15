<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeoCoding\Tests;

use McMatters\GoogleGeoCoding\GeoCoder;
use McMatters\GoogleGeoCoding\Models\Address;
use PHPUnit\Framework\TestCase;

use function mb_strpos;

use const false, null;

/**
 * Class GeoCoderTest
 *
 * @package McMatters\GoogleGeoCoding\Tests
 */
class GeoCoderTest extends TestCase
{
    /**
     * @var \McMatters\GoogleGeoCoding\GeoCoder
     */
    protected $geoCoder;

    /**
     * GeoCoderTest constructor.
     *
     * @param null $name
     * @param array $data
     * @param string $dataName
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->geoCoder = new GeoCoder(getenv('GOOGLE_GEOCODING_API_KEY'));
    }

    /**
     * Test method "getByAddress".
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     * @throws \McMatters\GoogleGeoCoding\Exceptions\GeoCodingException
     * @throws \PHPUnit\Framework\AssertionFailedError
     * @throws \PHPUnit\Framework\Exception
     * @throws \Throwable
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
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     * @throws \McMatters\GoogleGeoCoding\Exceptions\GeoCodingException
     * @throws \PHPUnit\Framework\AssertionFailedError
     * @throws \Throwable
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
