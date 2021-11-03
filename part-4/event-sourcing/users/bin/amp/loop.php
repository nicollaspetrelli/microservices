<?php

declare(strict_types=1);

use Amp\Loop;

require __DIR__ . '/../../vendor/autoload.php';

Loop::run(function () {
    echo "line 1\n";
    Loop::defer(function () {
        echo "line 3\n";
    });
    echo "line 2\n";

    Loop::delay(2000, function () {
        echo "line 4\n";
    });

    Loop::repeat(500, function ($watcherId) {
        static $i = 0;
        if ($i++ < 4) {
            echo "tick\n";
        } else {
            Loop::cancel($watcherId);
        }
    });
});
