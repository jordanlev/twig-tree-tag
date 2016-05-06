# jordan-tree

jordan-tree is a proof of concept of a Twig extension built to make tree traversals easy.

## Installation

```sh
composer require ninsuo/jordan-tree
```

## Idea

The `{% tree %}` tag works almost like `{% for %}`, but inside a `{% tree %}`, you can call `{% subtree var %}` to
run your `{% tree %}` block with the given var, recursively.

## Implementation Example

```jinja
{% tree name, submenu in menu %}
  {% if sibling.first %}<ul>{% endif %}
    <li>
        {{ name }}
        {% subtree submenu %}
    </li>
  {% if sibling.last %}</ul>{% endif %}
{% endtree %}
```

See the [demo](demo/) directory to see full implementations

## What is the `sibling` var?

The `sibling` var is the very same, and contain the same things as `loop` in a `{% for %}`. It is named
differently so you can use loops inside your `{% tree %}` bodies without conflicts.

## Nested trees ?

You can give a name to your trees to call `substree` without ambiguity.

```jinja
{% tree name, submenu in menu as treeA %}
  {% if sibling.first %}<ul>{% endif %}
    <li>
        {{ name }}
        {% subtree submenu with treeA %}
    </li>
  {% if sibling.last %}</ul>{% endif %}
{% endtree %}
```

## Tribute

This extension is called JordanTree for Jordan Lav, who first thought about it.
