<?php

namespace JordanLev\TwigTreeTag\Twig\TokenParser;

use JordanLev\TwigTreeTag\Twig\Node\TreeNode;
use Twig\Node\Expression\AssignNameExpression;
use Twig\Token;

class TreeTokenParser extends \Twig\TokenParser\AbstractTokenParser
{
    // {% tree
    public function getTag()
    {
        return 'tree';
    }

    public function parse(Token $token)
    {
        $lineno   = $token->getLine();
        $stream   = $this->parser->getStream();

        // key, item in items
        $targets  = $this->parser->getExpressionParser()->parseAssignmentExpression();
        $stream->expect(Token::OPERATOR_TYPE, 'in');
        $seq      = $this->parser->getExpressionParser()->parseExpression();

        // as treeA
        $as = 'default';
        if ($stream->nextIf(Token::NAME_TYPE, 'as')) {
            $as = $stream->expect(Token::NAME_TYPE)->getValue();
        }

        // %}
        $stream->expect(Token::BLOCK_END_TYPE);

        $data = array();
        while (true) {

            // backing up tag content
            $data[] = array(
                'type' => 'body',
                'node' => $this->parser->subparse(function(Token $token) {
                    return $token->test(array('subtree', 'endtree'));
                })
            );

            // {% subtree
            if ($stream->next()->getValue() == 'subtree') {

                // item
                $child = $this->parser->getExpressionParser()->parseExpression();

                // with treeA
                $with = $as;
                if ($stream->nextIf(Token::NAME_TYPE, 'with')) {
                    $with = $stream->expect(Token::NAME_TYPE)->getValue();
                }

                // %}
                $stream->expect(Token::BLOCK_END_TYPE);

                // backing up subtree details
                $data[] = array(
                    'type'  => 'subtree',
                    'with'  => $with,
                    'child' => $child,
                );

            // {% endtree
            } else {

                // %}
                $stream->expect(Token::BLOCK_END_TYPE);
                break;
            }
        }

        // key, item
        if (count($targets) > 1) {
            $keyTarget   = $targets->getNode(0);
            $keyTarget   = new AssignNameExpression($keyTarget->getAttribute('name'), $keyTarget->getTemplateLine());
            $valueTarget = $targets->getNode(1);
            $valueTarget = new AssignNameExpression($valueTarget->getAttribute('name'), $valueTarget->getTemplateLine());

        // (implicit _key,) item
        } else {
            $keyTarget   = new AssignNameExpression('_key', $lineno);
            $valueTarget = $targets->getNode(0);
            $valueTarget = new AssignNameExpression($valueTarget->getAttribute('name'), $valueTarget->getTemplateLine());
        }

        return new TreeNode($keyTarget, $valueTarget, $seq, $as, $data, $lineno, $this->getTag());
    }
}
