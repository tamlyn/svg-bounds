SVG Bounds in PHP
-----------------

Calculates smallest bounding rectangle for given SVG path string.

Only handles paths composed of straight lines. Bezier and arc commands throw an exception. Returns an object with top left (x1, y1) bottom right (x2, x2) and size (width, height).

Example usage:

    $bounds = svgBounds('M 100 100L300 100 200 300z');
    echo $bounds->width; //200
 
 License: [Public domain](http://creativecommons.org/publicdomain/mark/1.0/)