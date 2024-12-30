<?php

declare(strict_types=1);

namespace Onepix\Stubs\WooCommerce;

use PhpParser\Node;
use StubsGenerator\NodeVisitor;

class Visitor extends NodeVisitor
{
    private ConstantsExtractor $constantsExtractor;
    private VersionExtractor $versionExtractor;

    public function __construct()
    {
        $this->versionExtractor = new VersionExtractor();
        $this->constantsExtractor = new ConstantsExtractor(
            $this,
            $this->versionExtractor
        );
    }

    public function enterNode(Node $node)
    {
        $this->constantsExtractor->processNode($node);

        return parent::enterNode($node);
    }
}
