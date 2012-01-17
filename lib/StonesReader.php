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
 * This class handles the parsing of the stones list
 *
 * @author Markus Tacker <m@coderbyheart.de>
 */
class StonesReader
{
    private $cacheFile;
    private $stones = array();

    public function __construct($csvFile)
    {
        $this->cacheFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'nowhere.csv';
        if (!is_file($this->cacheFile) || filemtime($this->cacheFile) < time() - 3600) copy($csvFile, $this->cacheFile);
    }

    private function fopen_utf8($filename, $mode)
    {
        $file = @fopen($filename, $mode);
        $bom = fread($file, 3);
        if ($bom != "\xEF\xBB\xBF")
            rewind($file, 0);
        return $file;
    }

    /**
     * @return Stone[]
     */
    public function getStones()
    {
        if (empty($this->stones)) {
            $stonesKey = md5(__DIR__) . '.stones';
            if (!apc_exists($stonesKey . '-ttl')) {
                apc_store($stonesKey . '-ttl', '-', 3600);
                $stones = array();
                $fp = $this->fopen_utf8($this->cacheFile, 'r');
                while ($stoneData = fgetcsv($fp)) {
                    $stone = new Stone();
                    $stone->setNumber($stoneData[0]);
                    $stone->setCountry($stoneData[1]);
                    $stone->setLocality($stoneData[2]);
                    $stone->setPerson($stoneData[3]);
                    $stone->setLat($stoneData[4]);
                    $stone->setLng($stoneData[5]);
                    $apcKey = md5(__DIR__) . '.nowhere-image-size-' . $stone->getNumber();
                    if (!apc_exists($apcKey)) {
                        apc_store($apcKey, getimagesize('media' . DIRECTORY_SEPARATOR . 'stones' . DIRECTORY_SEPARATOR . $stone->getNumber() . '-place-2048.jpg'));
                    }
                    $sizeinfo = apc_fetch($apcKey);
                    $width = $sizeinfo[0];
                    $height = $sizeinfo[1];
                    $stone->setWidth($width);
                    $stone->setHeight($height);
                    $stones[] = $stone;
                }
                apc_store($stonesKey, new ArrayObject($stones));
                fclose($fp);
            }
            $this->stones = apc_fetch($stonesKey);
        }
        return $this->stones;

    }

    /**
     * @param int $id
     * @return Stone
     */
    public function getStone($id)
    {
        foreach ($this->getStones() as $Stone) {
            if ($Stone->getNumber() == $id) return $Stone;
        }
        return null;
    }
}
