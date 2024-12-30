<?php
declare(strict_types=1);

namespace Onepix\Stubs\WooCommerce;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use StubsGenerator\NodeVisitor;

class ConstantsExtractor
{
    /** @var array<string> */
    private array $functionsWithConstants = [
        'define_constants',
    ];

    public function __construct(
        private readonly NodeVisitor      $visitor,
        private readonly VersionExtractor $versionExtractor,
    )
    {
    }

    public function processNode(Node $node): void
    {
        if ($node instanceof Class_) {
            $this->versionExtractor->maybeExtractVersion($node);
            return;
        }

        if (!($node instanceof ClassMethod)) {
            return;
        }

        if (!in_array($node->name->toString(), $this->functionsWithConstants, true)) {
            return;
        }

        foreach ($node->stmts as $stmt) {
            if (!$this->isDefineStatement($stmt)) {
                continue;
            }

            $methodCall = $stmt->expr;
            if (count($methodCall->args) !== 2) {
                continue;
            }

            $methodCall->args[1]->value = $this->versionExtractor->processValue($methodCall->args[1]->value);

            $defineCall = new Expression(
                new FuncCall(
                    new Name('define'),
                    $methodCall->args
                )
            );

            $this->visitor->addNodeToGlobalNamespace($defineCall);
        }

        $node->stmts = [];
    }

    private function isDefineStatement(Node $stmt): bool
    {
        return $stmt instanceof Expression &&
            $stmt->expr instanceof MethodCall &&
            $stmt->expr->name instanceof Identifier &&
            $stmt->expr->name->toString() === 'define';
    }
}
