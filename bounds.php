<?php

/**
 * Calculate smallest bounding rectangle for given SVG path string. 
 *
 * Only handles paths composed of straight lines. Bezier and arc commands throw an exception.
 * Returns an object with top left (x1, y1) bottom right (x2, x2) and size (width, height).
 *
 * Example usage:
 *   $bounds = svgBounds('M 100 100L300 100 200 300z');
 *   echo $bounds->width; //200
 *
 * @param string $pathString SVG path command
 * @return stdClass
 * @author Tamlyn Rhodes <http://tamlyn.org>
 * @license http://creativecommons.org/publicdomain/mark/1.0/ Public Domain
 */
function svgBounds($pathString) {
	//match each command sequence starting with a letter
	preg_match_all('/([mlvhz][^mlvhz]*)/i', $pathString, $commands);

	//handle initial move command
	$firstMove = array_shift($commands[0]);
	preg_match_all('/(-?[0-9.]+)/', $firstMove, $matches);
	$pt = $matches[1];
	$bounds = (object) array('x1' => $pt[0], 'y1' => $pt[1], 'x2' => $pt[0], 'y2' => $pt[1]);

	//loop through successive command sequences
	foreach ($commands[0] as $command) {

		//match numbers in string
		preg_match_all('/(-?[0-9.]+)/', $command, $matches);

		//loop through numbers and move point according to command
		$i=0;
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
					throw new RuntimeException("Unhandled path command: ".$command[0]);
			}

			//expand bounds
			$bounds->x1 = min($bounds->x1, $pt[0]);
			$bounds->y1 = min($bounds->y1, $pt[1]);
			$bounds->x2 = max($bounds->x2, $pt[0]);
			$bounds->y2 = max($bounds->y2, $pt[1]);
		}

	}

	//calculate width and height
	$bounds->width = $bounds->x2 - $bounds->x1;
	$bounds->height = $bounds->y2 - $bounds->y1;

	return $bounds;
}