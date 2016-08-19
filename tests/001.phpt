--TEST--
Check for vips presence
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  echo "vips extension is available";
?>
--EXPECT--
vips extension is available
