--TEST--
operator expansions all work
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
  $image = Vips\Image::new_from_file($filename);

  $pass = TRUE;

  /* The original value of pixel (0, 0).
   */
  $pixel = [39, 38, 34];

  /* pow()
   */
  $true = map_numeric($pixel, function ($value) {
  	return $value ** 2; 
  });
  if ($image->pow(2)->getpoint(0, 0) != $true) { 
  	$pass = FALSE;
  }

  /* wop()
   */
  $true = map_numeric($pixel, function ($value) {
  	return 2 ** $value;
  });
  if ($image->wop(2)->getpoint(0, 0) != $true) { 
  	$pass = FALSE;
  }

  /* remainder()
   */
  $true = map_numeric($pixel, function ($value) {
  	return $value % 2;
  });
  if ($image->remainder(2)->getpoint(0, 0) != $true) { 
  	$pass = FALSE;
  }
  $true = map_numeric($pixel, function ($value) {
  	return $value % $value;
  });
  if ($image->remainder($image)->getpoint(0, 0) != $true) { 
  	$pass = FALSE;
  }

  /* lshift()/rshift()
   */
  $true = map_numeric($pixel, function ($value) {
  	return $value << 2;
  });
  if ($image->lshift(2)->getpoint(0, 0) != $true) { 
  	$pass = FALSE;
  }
  $true = map_numeric($pixel, function ($value) {
  	return $value >> 2;
  });
  if ($image->rshift(2)->getpoint(0, 0) != $true) { 
  	$pass = FALSE;
  }

  /* and()/or()/eor()
   */
  $true = map_numeric($pixel, function ($value) {
  	return $value & 2;
  });
  if ($image->and(2)->getpoint(0, 0) != $true) { 
  	$pass = FALSE;
  }
  $true = map_numeric($pixel, function ($value) {
  	return $value | 2;
  });
  if ($image->or(2)->getpoint(0, 0) != $true) { 
  	$pass = FALSE;
  }
  $true = map_numeric($pixel, function ($value) {
  	return $value ^ 2;
  });
  if ($image->eor(2)->getpoint(0, 0) != $true) { 
  	$pass = FALSE;
  }

  /* more()moreeq().
   */
  $true = map_numeric($pixel, function ($value) {
  	if ($value > 38) {
		return 255;
	}
	else {
		return 0;
	}
  });
  if ($image->more(38)->getpoint(0, 0) != $true) { 
  	$pass = FALSE;
  }
  $true = map_numeric($pixel, function ($value) {
  	if ($value >= 38) {
		return 255;
	}
	else {
		return 0;
	}
  });
  if ($image->moreeq(38)->getpoint(0, 0) != $true) { 
  	$pass = FALSE;
  }

  /* more(), image arg.
   */
  $true = map_numeric($pixel, function ($value) {
  	if ($value > $value) {
		return 255;
	}
	else {
		return 0;
	}
  });
  if ($image->more($image)->getpoint(0, 0) != $true) { 
  	$pass = FALSE;
  }

  /* less()lesseq().
   */
  $true = map_numeric($pixel, function ($value) {
  	if ($value < 38) {
		return 255;
	}
	else {
		return 0;
	}
  });
  if ($image->less(38)->getpoint(0, 0) != $true) { 
  	$pass = FALSE;
  }
  $true = map_numeric($pixel, function ($value) {
  	if ($value <= 38) {
		return 255;
	}
	else {
		return 0;
	}
  });
  if ($image->lesseq(38)->getpoint(0, 0) != $true) { 
  	$pass = FALSE;
  }

  /* equal()noteq().
   */
  $true = map_numeric($pixel, function ($value) {
  	if ($value == 38) {
		return 255;
	}
	else {
		return 0;
	}
  });
  if ($image->equal(38)->getpoint(0, 0) != $true) { 
  	$pass = FALSE;
  }
  $true = map_numeric($pixel, function ($value) {
  	if ($value != 38) {
		return 255;
	}
	else {
		return 0;
	}
  });
  if ($image->noteq(38)->getpoint(0, 0) != $true) { 
  	$pass = FALSE;
  }

  if ($pass) {
	echo "pass";
  }
?>
--EXPECT--
pass
