--TEST--
rounding works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  function map_numeric($value, $func)
  {
  	if (is_numeric($value)) {
  		$value = $func($value);
  	}
  	else if (is_array($value)) {
  		array_walk_recursive($value, function (&$item, $key) use ($func) {
			$item = map_numeric($item, $func);
		});
  	}

  	return $value;
  } 

  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $image = Vips\Image::newFromFile($filename);
  $image = $image->add([0.1, 1.5, 0.9]);

  $pass = TRUE;

  /* The original value of pixel (0, 0).
   */
  $pixel = [39.1, 39.5, 34.9];

  /* floor()
   */
  $true = map_numeric($pixel, function ($value) {
  	return floor($value);
  });
  if ($image->floor()->getpoint(0, 0) != $true) { 
  	$pass = FALSE;
  }

  /* ceil()
   */
  $true = map_numeric($pixel, function ($value) {
  	return ceil($value);
  });
  if ($image->ceil()->getpoint(0, 0) != $true) { 
  	$pass = FALSE;
  }

  /* rint()
   */
  $true = map_numeric($pixel, function ($value) {
  	return round($value);
  });
  if ($image->rint()->getpoint(0, 0) != $true) { 
  	echo "rint fails\n";
  	$pass = FALSE;
  }

  if ($pass) {
	echo "pass";
  }
?>
--EXPECT--
pass
