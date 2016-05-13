<?php

namespace Fuz\Jordan\Twig\TokenParser;

use Fuz\Jordan\Twig\Node\TreeNode;

class TreeTokenParser extends \Twig_TokenParser
{
    // {% tree
    public function getTag()
    {
        return 'tree';
    }

    public function parse(\Twig_Token $token)
    {
        $lineno   = $token->getLine();
        $stream   = $this->parser->getStream();

        // key, item in items
        $targets  = $this->parser->getExpressionParser()->parseAssignmentExpression();
        $stream->expect(\Twig_Token::OPERATOR_TYPE, 'in');
        $seq      = $this->parser->getExpressionParser()->parseExpression();

        // as treeA
        $as = 'default';
        if ($stream->nextIf(\Twig_Token::NAME_TYPE, 'as')) {
            $as = $stream->expect(\Twig_Token::NAME_TYPE)->getValue();
        }

        // %}
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $data = array();
        while (true) {

            // backing up tag content
            $data[] = array(
                'type' => 'body',
                'node' => $this->parser->subparse(function(\Twig_Token $token) {
                    return $token->test(array('subtree', 'endtree'));
                })
            );

            // {% subtree
            if ($stream->next()->getValue() == 'subtree') {

                // item
                $child = $this->parser->getExpressionParser()->parseExpression();

                // with treeA
                $with = $as;
                if ($stream->nextIf(\Twig_Token::NAME_TYPE, 'with')) {
                    $with = $stream->expect(\Twig_Token::NAME_TYPE)->getValue();
                }

                // %}
                $stream->expect(\Twig_Token::BLOCK_END_TYPE);

                // backing up subtree details
                $data[] = array(
                    'type'  => 'subtree',
                    'with'  => $with,
                    'child' => $child,
                );

            // {% endtree
            } else {

                // %}
                $stream->expect(\Twig_Token::BLOCK_END_TYPE);
                break;
            }
        }

        // key, item
        if (count($targets) > 1) {
            $keyTarget   = $targets->getNode(0);
            $keyTarget   = new \Twig_Node_Expression_AssignName($keyTarget->getAttribute('name'), $keyTarget->getLine());
            $valueTarget = $targets->getNode(1);
            $valueTarget = new \Twig_Node_Expression_AssignName($valueTarget->getAttribute('name'), $valueTarget->getLine());

        // (implicit _key,) item
        } else {
            $keyTarget   = new \Twig_Node_Expression_AssignName('_key', $lineno);
            $valueTarget = $targets->getNode(0);
            $valueTarget = new \Twig_Node_Expression_AssignName($valueTarget->getAttribute('name'), $valueTarget->getLine());
        }

        return new TreeNode($keyTarget, $valueTarget, $seq, $as, $data, $lineno, $this->getTag());
    }
}
