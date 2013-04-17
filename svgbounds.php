<?php

/**
 * Calculate smallest bounding rectangle for given SVG path strings and points. 
 *
 * Only handles paths composed of straight lines. Bezier and arc commands throw an exception.
 *
 * Example usage:
 *   $bounds = SvgBounds::fromPath('M 100 100L300 100 200 300z');
 *   echo $bounds->getWidth(); //200
 *   $bounds->extend(350, 100);
 *   echo $bounds->getWidth(); //250
 *
 * @author Tamlyn Rhodes <http://tamlyn.org>
 * @license http://creativecommons.org/publicdomain/mark/1.0/ Public Domain
 */
class SvgBounds
{
	/**
	 * @var int
	 */
	public $x1, $y1, $x2, $y2;

	/**
	 * Construct empty bounds object
	 */
	public function __construct() {
		$this->x1 = PHP_INT_MAX;
		$this->y1 = PHP_INT_MAX;
		$this->x2 = -PHP_INT_MAX;
		$this->y2 = -PHP_INT_MAX;
	}

	/**
	 * @param string $pathString
	 * @return SVGBounds
	 * @throws RuntimeException
	 */
	public static function fromPath($pathString) {
		//match each command sequence starting with a letter
		preg_match_all('/([mlvhz][^mlvhz]*)/i', $pathString, $commands);

		$pt = array(0, 0);
		$bounds = new self();

		//loop through command sequences
		foreach ($commands[0] as $command) {

			//match numbers in string
			//see http://www.w3.org/TR/SVG/paths.html#PathDataBNF for allowed number formats
			preg_match_all('/((\+|-)?\d+(\.\d+)?(e(\+|-)?\d+)?)/i', $command, $matches);

			//loop through numbers and move point according to command
			$i = 0;
			while ($i < count($matches[1])) {

				//update current position
				switch ($command[0]) {
					case 'm' :
					case 'l' :
						$pt[0] += $matches[1][$i++];
						$pt[1] += $matches[1][$i++];
						break;
					case 'M' :
					case 'L' :
						$pt[0] = $matches[1][$i++];
						$pt[1] = $matches[1][$i++];
						break;
					case 'v' :
						$pt[1] += $matches[1][$i++];
						break;
					case 'V' :
						$pt[1] = $matches[1][$i++];
						break;
					case 'h' :
						$pt[0] += $matches[1][$i++];
						break;
					case 'H' :
						$pt[0] = $matches[1][$i++];
						break;
					case 'z' :
					case 'Z' :
						break;
					default :
						throw new RuntimeException("Unhandled path command: " . $command[0]);
				}

				//expand bounds
				$bounds->extend($pt[0], $pt[1]);
			}

		}

		return $bounds;
	}

	/**
	 * @param int $x
	 * @param int $y
	 * @return \SVGBounds
	 */
	public function extend($x, $y) {
		$this->x1 = min($this->x1, $x);
		$this->y1 = min($this->y1, $y);
		$this->x2 = max($this->x2, $x);
		$this->y2 = max($this->y2, $y);

		return $this;
	}

	/**
	 * @param SVGBounds $otherBounds
	 * @return \SVGBounds
	 */
	public function union(self $otherBounds) {
		$this->extend($otherBounds->x1, $otherBounds->y1);
		$this->extend($otherBounds->x2, $otherBounds->y2);

		return $this;
	}

	/**
	 * @param string $pathString
	 * @return SVGBounds
	 */
	public function unionPath($pathString) {
		return $this->union(self::fromPath($pathString));
	}

	/**
	 * @return int
	 */
	public function getWidth() {
		return $this->x2 - $this->x1;
	}

	/**
	 * @return int
	 */
	public function getHeight() {
		return $this->y2 - $this->y1;
	}

	/**
	 * @return array
	 */
	public function toArray() {
		return array_merge(get_object_vars($this), array(
			'width' => $this->getWidth(),
			'height' => $this->getHeight()
		));
	}
}
