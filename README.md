# jordan-tree

jordan-tree is a proof of concept of a Twig extension built to make tree traversals easy.

## Installation



## Example

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




# Tribute

This extension is called JordanTree for Jordan Lav, who first thought about it.
