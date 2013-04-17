SVG Bounds in PHP
-----------------

 Calculate smallest bounding rectangle for given SVG path strings and points. 
 
 Only handles paths composed of straight lines. Bezier and arc commands throw an exception.
 
 Example usage:
     $bounds = SvgBounds::fromPath('M 100 100L300 100 200 300z');
     echo $bounds->getWidth(); //200
     $bounds->extend(350, 100);
     echo $bounds->getWidth(); //250
 
 License: [Public domain](http://creativecommons.org/publicdomain/mark/1.0/)