# jordan-tree

A Twig extension for succinctly traversing nested lists (e.g. navigation menus).

## Requirements

Requires PHP 5.4 or higher (due to usage of `$this` in anonymous function of compiled templates -- unfortunately this is the only way to achieve the desired recursion of a block calling itself).

## Idea

The `{% tree %}` tag works almost like `{% for %}`, but inside a `{% tree %}` you can call `{% subtree var %}` to
recursively run your `{% tree %}` block with the given `var`. The primary use-case for this tag is nested navigation menus.

This extension is called jordan-tree for [Jordan Lev](https://github.com/jordanlev), who suggested the idea to Alain Tiemblo.

## Usage Example

In this example, `menu` is an array of objects, each containing `name`, `url`, and `children` properties (`children` is itself an array of objects with the same properties, etc).

```jinja
{% tree item in menu %}
  {% if treeloop.first %}<ul>{% endif %}
    <li>
        <a href="{{ item.url }}">{{ item.name }}</a>
        {% subtree item.children %}
    </li>
  {% if treeloop.last %}</ul>{% endif %}
{% endtree %}
```

Just like a `{% for %}` loop, you can access the key of each list item:
```jinja
{% tree key, item in menu %}
  <li>
    <b>Item {{ key }}</b>: {{ item.name }}
    {% subtree item.children %}
  </li>
{% endtree %}
```

See the [demo directory](demo/) for more examples


## What is the `treeloop` var?

The `treeloop` var serves the same purpose inside a `{% tree %}` tag as the `loop` var does inside a `{% for %}` tag. It is named differently so that you can still use `loop` when you have a `{% for %}` tag inside your `{% tree %}` tag (otherwise they would conflict).


## What if I want a tree tag inside another tree tag?

To handle the edge case where you want to start a new tree inside another tree (that is, a new tree "root" with its own markup), use `as` in your `{% tree %}` tag to assign each tree to a var name, then pass it into `subtree` via `with`. This allows Twig to know which `{% tree %}` should be called when it comes across the `{% subtree %}` tag. For example...

```jinja
{% tree item in menu as treeA %}
  {% if treeloop.first %}<ul>{% endif %}
    <li>
        {{ item.name }}
        {% subtree item.children with treeA %}
        
        <h2>Some other tree (that has its own "root", not a sub-tree of treeA):</h2>
        {% tree otherthing in item.otherthings as treeB %}
          {{ otherthings.name }}
          {% subtree otherthings.subitems with treeB %}
          {# We use "with treeB" above so Twig knows which parent tree tag to call #}
        {% endtree %}
    </li>
  {% if treeloop.last %}</ul>{% endif %}
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
