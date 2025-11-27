<?php

namespace QEEP\TwigTreeTag\Twig\TokenParser;

use QEEP\TwigTreeTag\Twig\Node\TreeNode;

class TreeTokenParser extends \Twig\TokenParser\AbstractTokenParser
{
    // {% tree
    public function getTag()
    {
        return 'tree';
    }

    public function parse(\Twig\Token $token)
    {
        $lineno   = $token->getLine();
        $stream   = $this->parser->getStream();

        // key, item in items
        $targets  = $this->parseAssignmentExpression();
        $stream->expect(\Twig\Token::OPERATOR_TYPE, 'in');
        $seq      = $this->parser->parseExpression();

        // as treeA
        $as = 'default';
        if ($stream->nextIf(\Twig\Token::NAME_TYPE, 'as')) {
            $as = $stream->expect(\Twig\Token::NAME_TYPE)->getValue();
        }

        // %}
        $stream->expect(\Twig\Token::BLOCK_END_TYPE);

        $data = array();
        while (true) {

            // backing up tag content
            $data[] = array(
                'type' => 'body',
                'node' => $this->parser->subparse(function(\Twig\Token $token) {
                    return $token->test(array('subtree', 'endtree'));
                })
            );

            // {% subtree
            if ($stream->next()->getValue() == 'subtree') {

                // item
                $child = $this->parser->parseExpression();

                // with treeA
                $with = $as;
                if ($stream->nextIf(\Twig\Token::NAME_TYPE, 'with')) {
                    $with = $stream->expect(\Twig\Token::NAME_TYPE)->getValue();
                }

                // %}
                $stream->expect(\Twig\Token::BLOCK_END_TYPE);

                // backing up subtree details
                $data[] = array(
                    'type'  => 'subtree',
                    'with'  => $with,
                    'child' => $child,
                );

            // {% endtree
            } else {

                // %}
                $stream->expect(\Twig\Token::BLOCK_END_TYPE);
                break;
            }
        }

        // key, item
        if (count($targets) > 1) {
            $keyTarget   = $targets->getNode(0);
            $keyTarget   = new \Twig\Node\Expression\AssignNameExpression(
                $keyTarget->getAttribute('name'),
                $keyTarget->getTemplateLine()
            );

            $valueTarget = $targets->getNode(1);
            $valueTarget = new \Twig\Node\Expression\AssignNameExpression(
                $valueTarget->getAttribute('name'),
                $valueTarget->getTemplateLine()
            );

        // (implicit _key,) item
        } else {
            $keyTarget   = new \Twig\Node\Expression\AssignNameExpression('_key', $lineno);
            $valueTarget = $targets->getNode(0);
            $valueTarget = new \Twig\Node\Expression\AssignNameExpression(
                $valueTarget->getAttribute('name'),
                $valueTarget->getTemplateLine()
            );
        }

        return new TreeNode($keyTarget, $valueTarget, $seq, $as, $data, $lineno, $this->getTag());
    }
}
