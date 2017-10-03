<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeocoding\Tests;

use McMatters\GoogleGeocoding\GeoCoder;
use PHPUnit\Framework\TestCase;

/**
 * Class GeocoderTest
 *
 * @package McMatters\GoogleGeocoding\Tests
 */
class GeocoderTest extends TestCase
{
    /**
     * @var GeoCoder
     */
    protected $geocoder;

    /**
     * GeocoderTest constructor.
     *
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->geocoder = new GeoCoder(getenv('GOOGLE_GEOCODING_API_KEY'));
    }

    /**
     * Test method "getByAddress".
     */
    public function testGetByAddress()
    {
        $addresses = $this->geocoder->getByAddress('Kyiv');
        $this->assertTrue($addresses->isNotEmpty());
        $this->assertCount(1, $addresses);
        $this->assertNotEmpty($addresses->first());
    }
}
