<?php

// +----------------------------------------------------+
// | The Project Nowhere website is free software:      |
// | you can redistribute it and/or modify it under     |
// | the terms of the GNU General Public License as     |
// | published by the Free Software Foundation,         |
// | either version 3 of the License, or (at your       |
// | option) any later version.                         |
// |                                                    |
// | In addition you are required to retain all         |
// | author attributions provided in this software      |
// | and attribute all modifications made by you        |
// | clearly and in an appropriate way.                 |
// |                                                    |
// | This software is distributed in the hope that      |
// | it will be useful, but WITHOUT ANY WARRANTY;       |
// | without even the implied warranty of               |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR        |
// | PURPOSE.  See the GNU General Public License for   |
// | more details.                                      |
// |                                                    |
// | You should have received a copy of the GNU         |
// | General Public License along with this software.   |
// | If not, see <http://www.gnu.org/licenses/>.        |
// +----------------------------------------------------+

/**
 * Model for a stone
 *
 * @author Markus Tacker <m@coderbyheart.de>
 */

class Stone
{
    /**
     * @var int
     */
    private $number;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $locality;

    /**
     * @var string
     */
    private $person;

    /**
     * @var string
     */
    private $lat;

    /**
     * @var string
     */
    private $lng;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $lat
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    /**
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @return string
     */
    public function getLatc()
    {
        return $this->cleanCoord($this->lat);
    }

    /**
     * @param string $lng
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
    }

    /**
     * @return string
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @return string
     */
    public function getLngc()
    {
        return $this->cleanCoord($this->lng);
    }

    /**
     * @param string $locality
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;
    }

    /**
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * @param int $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * @return string
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param string $coord
     * @return string
     */
    protected function cleanCoord($coord)
    {
        $coord = str_replace("‘", "'", $coord);
        $coord = preg_replace('/[^+°\'\.0-9]/', '', $coord);
        if ($coord[0] == '+') {
            $coord = substr($coord, 1);
        } else {
            $coord = '-' . $coord;
        }
        return $coord;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return float
     */
    public function getAr()
    {
        return $this->getHeight() / $this->getWidth();
    }
}