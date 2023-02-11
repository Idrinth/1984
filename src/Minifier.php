<?php

namespace De\Idrinth\Project1984;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Nop;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

final class Minifier
{
    public function minify(string $phpCode, bool $indent = true): string
    {
        $isFullFile = true;
        if (substr($phpCode, 0, 5) !== '<?php') {
            $phpCode = "<?php $phpCode";
            $isFullFile = false;
        }
        $factory = new ParserFactory();
        $parser = $factory->create(ParserFactory::PREFER_PHP5);
        $traverser = new NodeTraverser();
        $printer = new SimplePrinter($indent);
        $traverser->addVisitor(new class extends NodeVisitorAbstract {
            /**
             * @suppress PhanPluginUnknownMethodReturnType, PhanUndeclaredProperty
             */
            public function leaveNode(Node $node)
            {
                if ($node instanceof Nop) {
                    return NodeTraverser::REMOVE_NODE;
                }
                if ($node instanceof If_) {
                    if (!$node->elseifs || count($node->elseifs) === 0) {
                        $cond = $node->cond;
                        if ($cond instanceof Equal || $cond instanceof Identical) {
                            if ($cond->left instanceof String_ && $cond->right instanceof String_) {
                                if ($cond->left->value !== $cond->right->value) {
                                    return $node->else ?? NodeTraverser::REMOVE_NODE;
                                }
                                return $node->stmts;
                            }
                        }
                    }
                }
                return null;
            }
        });
        return ($isFullFile ? "<?php\n" : '') . $printer->prettyPrint($traverser->traverse($parser->parse($phpCode)));
    }
}
