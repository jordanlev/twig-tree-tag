<?php

namespace Jordan\Twig\Node;

class TreeNode extends \Twig_Node
{
    public function __construct(\Twig_Node_Expression_AssignName $keyTarget, \Twig_Node_Expression_AssignName $valueTarget, \Twig_Node_Expression $seq,  $as, array $data, $lineno, $tag)
    {
        parent::__construct([
            'key_target'   => $keyTarget,
            'value_target' => $valueTarget,
            'seq'          => $seq,
           ], [
            'data'         => $data,
            'as'           => $as,
           ], $lineno, $tag);
    }

    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
        ;

        // $tree_treeA = function($data) use (&$context, &$tree_treeA) {
        $compiler
            ->write("\$tree_")
            ->raw($this->getAttribute('as'))
            ->raw(" = ")
            ->raw("function(\$data) use (&\$context, &\$tree_")
            ->raw($this->getAttribute('as'))
            ->raw(") {\n")
            ->indent()
        ;

        // backuping local scope context
        $compiler
            ->write("\$context['_parent'] = \$context;\n")
            ->write("\$context['_seq'] = twig_ensure_traversable(\$data);\n")
        ;

        // initializing sibling variable
        $compiler
            ->write("\$context['sibling'] = array(\n")
            ->write("  'parent' => \$context['_parent'],\n")
            ->write("  'index0' => 0,\n")
            ->write("  'index'  => 1,\n")
            ->write("  'first'  => true,\n")
            ->write(");\n")
            ->write("if (is_array(\$context['_seq']) || (is_object(\$context['_seq']) && \$context['_seq'] instanceof Countable)) {\n")
            ->indent()
            ->write("\$length = count(\$context['_seq']);\n")
            ->write("\$context['sibling']['revindex0'] = \$length - 1;\n")
            ->write("\$context['sibling']['revindex'] = \$length;\n")
            ->write("\$context['sibling']['length'] = \$length;\n")
            ->write("\$context['sibling']['last'] = 1 === \$length;\n")
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
                        ->raw(");\n")
                        ->outdent()
                        ->write("}")
                    ;
                    break;

                default:
                    break;
            }
        }

        // updating sibling context
        $compiler
            ->write("++\$context['sibling']['index0'];\n")
            ->write("++\$context['sibling']['index'];\n")
            ->write("\$context['sibling']['first'] = false;\n")
            ->write("if (isset(\$context['sibling']['length'])) {\n")
            ->indent()
            ->write("--\$context['sibling']['revindex0'];\n")
            ->write("--\$context['sibling']['revindex'];\n")
            ->write("\$context['sibling']['last'] = 0 === \$context['sibling']['revindex0'];\n")
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
           ->write("\$_parent = \$context['_parent'];\n")
           ->write('unset($context[\'_seq\'], $context[\'_iterated\'], $context[\''.$this->getNode('key_target')->getAttribute('name').'\'], $context[\''.$this->getNode('value_target')->getAttribute('name').'\'], $context[\'_parent\'], $context[\'sibling\']);'."\n")
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
            ->raw(");\n")
        ;
    }
}
