<?php
declare(strict_types=1);

use StubsGenerator\Finder;

return Finder::create()
    ->in('source/woocommerce')
	->notPath('source/woocommerce/lib')
	->notPath('source/woocommerce/vendor')
    ->sortByName(true);
