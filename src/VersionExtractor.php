<?php
declare(strict_types=1);

namespace Onepix\Stubs\WooCommerce;

use PhpParser\Node;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;

class VersionExtractor
{
    public const CLASS_NAME = 'WooCommerce';

    private ?string $version = null;

    public function maybeExtractVersion(Node $node): void
    {
        if ($node instanceof Node\Stmt\Class_) {
            if ($node->name->toString() === self::CLASS_NAME) {
                foreach ($node->stmts as $stmt) {
                    if ($stmt instanceof Node\Stmt\Property) {
                        foreach ($stmt->props as $prop) {
                            if ($prop->name->toString() === 'version' && $prop->default instanceof String_) {
                                $this->version = $prop->default->value;
                                break 2;
                            }
                        }
                    }
                }
            }
        }
    }

    public function processValue(Node $node): ?Node
    {
        if ($node instanceof PropertyFetch
            && $node->var instanceof Node\Expr\Variable
            && $node->var->name === 'this'
            && $node->name instanceof Identifier
            && $node->name->toString() === 'version'
            && $this->getVersion() !== null
        ) {
            return new String_($this->getVersion());
        }

        return $node;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version ?? '';
    }
}
