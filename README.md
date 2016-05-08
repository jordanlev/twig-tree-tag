# jordan-tree

jordan-tree is a proof of concept of a Twig extension built to make tree traversals easy.

## Idea

The `{% tree %}` tag works almost like `{% for %}`, but inside a `{% tree %}`, you can call `{% subtree var %}` to
run your `{% tree %}` block with the given `var`, recursively. This extension is called jordan-tree for
[Jordan Lav](https://github.com/jordanlev), who first thought about it.

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

## Nested trees?

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

## Installation

```sh
composer require ninsuo/jordan-tree
```

## Usage

```php
$loader = require __DIR__.'/vendor/autoload.php';

$twig = new \Twig_Environment(
    new \Twig_Loader_Filesystem(__DIR__.'/view/')
);

$twig->addExtension(new Fuz\Jordan\Twig\Extension\TreeExtension());

// (...)
```

## License

The MIT License (MIT)

Please read the [LICENSE](LICENSE) file for more details.
