<?php

namespace De\Idrinth\Project1984;

use PhpParser\Comment;
use PhpParser\PrettyPrinter\Standard;

class SimplePrinter extends Standard
{
    private bool $indent;

    public function __construct(bool $indent = true)
    {
        parent::__construct();
        $this->indent = $indent;
    }
    /**
     * @suppress PhanPluginUnknownMethodReturnType, PhanTypeMismatchPropertyProbablyReal
     */
    protected function resetState()
    {
        $this->indentLevel = 0;
        $this->nl = $this->indent ? "\n" : '';
        $this->origTokens = null;
    }
    /**
     * @param Comment[] $comments
     */
    protected function pComments(array $comments): string
    {
        return '';
    }
    /**
     * @suppress PhanPluginUnknownMethodReturnType
     */
    protected function indent()
    {
        if (!$this->indent) {
            return;
        }
        parent::indent();
    }
    /**
     * @suppress PhanPluginUnknownMethodReturnType
     */
    protected function outdent()
    {
        if (!$this->indent) {
            return;
        }
        parent::outdent();
    }
}
