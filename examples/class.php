#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Jcupitt\Vips;

const LOG_FORMAT = "[%datetime%] %level_name%: %message% %context%\n";
const DATE_FORMAT = "Y-m-d\TH:i:sP";

Vips\Image::setLogger(new class implements Psr\Log\LoggerInterface {
    // Use the LoggerTrait so that we only have to implement the generic
    // log method.
    use Psr\Log\LoggerTrait;

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        // `Vips\Image` to string convert
        array_walk_recursive($context, function (&$value) {
            if ($value instanceof Vips\Image) {
                $value = (string) $value;
            }
        });

        $strParams = [
            '%datetime%' => date(DATE_FORMAT),
            '%level_name%' => $level,
            '%message%' => $message,
            '%context%' => json_encode(
                $context,
                JSON_UNESCAPED_SLASHES |
                JSON_UNESCAPED_UNICODE |
                JSON_PRESERVE_ZERO_FRACTION
            ),
        ];

        echo strtr(LOG_FORMAT, $strParams);
    }
});

$image = Vips\Image::newFromFile($argv[1]);

echo "width = " . $image->width . "\n";

$image = $image->invert();

$image->writeToFile($argv[2]);

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: expandtab sw=4 ts=4 fdm=marker
 * vim<600: expandtab sw=4 ts=4
 */
