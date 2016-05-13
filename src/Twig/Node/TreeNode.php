<?php

namespace Fuz\Jordan\Twig\Node;

class TreeNode extends \Twig_Node
{
    public function __construct(\Twig_Node_Expression_AssignName $keyTarget, \Twig_Node_Expression_AssignName $valueTarget, \Twig_Node_Expression $seq,  $as, array $data, $lineno, $tag)
    {
        parent::__construct(array(
            'key_target'   => $keyTarget,
            'value_target' => $valueTarget,
            'seq'          => $seq,
           ), array(
            'data'         => $data,
            'as'           => $as,
           ), $lineno, $tag);
    }

    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
        ;

        // $tree_treeA = function($data, $level) use (&$context, &$tree_treeA) {
        $compiler
            ->write("\$tree_")
            ->raw($this->getAttribute('as'))
            ->raw(" = ")
            ->raw("function(\$data, \$level) use (&\$context, &\$tree_")
            ->raw($this->getAttribute('as'))
            ->raw(") {\n")
            ->indent()
        ;

        // backuping local scope context
        $compiler
            ->write("\$context['_parent'][\$level] = \$context;\n")
            ->write("\$context['_seq'] = twig_ensure_traversable(\$data);\n")
        ;

        // initializing treeloop variable
        $compiler
            ->write("\$context['treeloop'] = array(\n")
            ->write("  'parent' => \$context['_parent'][\$level],\n")
            ->write("  'index0' => 0,\n")
            ->write("  'index'  => 1,\n")
            ->write("  'first'  => true,\n")
            ->write(");\n")
            ->write("if (is_array(\$context['_seq']) || (is_object(\$context['_seq']) && \$context['_seq'] instanceof Countable)) {\n")
            ->indent()
            ->write("\$length = count(\$context['_seq']);\n")
            ->write("\$context['treeloop']['revindex0'] = \$length - 1;\n")
            ->write("\$context['treeloop']['revindex'] = \$length;\n")
            ->write("\$context['treeloop']['length'] = \$length;\n")
            ->write("\$context['treeloop']['last'] = 1 === \$length;\n")
            ->outdent()
            ->write("}\n")
        ;

        // starting loop
        $compiler
            ->write("foreach (\$context['_seq'] as ")
            ->subcompile($this->getNode('key_target'))
            ->raw(' => ')
            ->subcompile($this->getNode('value_target'))
            ->raw(") {\n")
            ->indent()
        ;

        // tag's body
        foreach ($this->getAttribute('data') as $data) {
            switch ($data['type']) {

                // case #1: a simple Twig_Node_Body
                case 'body':
                    $compiler->subcompile($data['node']);
                    break;

                // case #2: recursive call to $tree_treeA
                case 'subtree':
                    $compiler
                        ->write("if (is_array(\$context['_seq']) || (is_object(\$context['_seq']) && \$context['_seq'] instanceof Traversable)) {\n")
                        ->indent()
                        ->write("\$tree_")
                        ->raw($data['with'])
                        ->raw("(")
                        ->subcompile($data['child'])
                        ->raw(", (\$level + 1));\n")
                        ->outdent()
                        ->write("}")
                    ;
                    break;

                default:
                    break;
            }
        }

        // updating treeloop context
        $compiler
            ->write("++\$context['treeloop']['index0'];\n")
            ->write("++\$context['treeloop']['index'];\n")
            ->write("\$context['treeloop']['first'] = false;\n")
            ->write("if (isset(\$context['treeloop']['length'])) {\n")
            ->indent()
            ->write("--\$context['treeloop']['revindex0'];\n")
            ->write("--\$context['treeloop']['revindex'];\n")
            ->write("\$context['treeloop']['last'] = 0 === \$context['treeloop']['revindex0'];\n")
            ->outdent()
            ->write("}\n")
        ;

        // ending loop
        $compiler
            ->outdent()
            ->write("}\n")
        ;

        // recovering local scope context and cleaning up
        $compiler
           ->write("\$_parent = \$context['_parent'][\$level];\n")
           ->write('unset($context[\'_seq\'], $context[\'_iterated\'], $context[\''.$this->getNode('key_target')->getAttribute('name').'\'], $context[\''.$this->getNode('value_target')->getAttribute('name').'\'], $context[\'_parent\'][$level], $context[\'treeloop\']);'."\n")
           ->write("\$context = array_intersect_key(\$context, \$_parent) + \$_parent;\n")
        ;

        // };
        // $tree_treeA($context["items"]);
        $compiler
            ->outdent()
            ->write("};\n")
            ->write("\$tree_")
            ->raw($this->getAttribute('as'))
            ->raw("(")
            ->subcompile($this->getNode('seq'))
            ->raw(", 0);\n")
        ;
    }
}
