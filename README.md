# Utils
Utils object is desinged as a swiss knife of functions that is normally used for simple tasks as converting decimals to string fractions, checking values, basic cross scripting and curl initializers with easy to remember arguments.

It requires xTags another piece of code to generate html tag structures with PHP object oriented methods.

## To Install
Using terminal, install with composer:

```
$> composer require raphievila/utils
```

# USE
I've added some comments to the script that shows how to use each tool. There is no specific use for the tools, I mostly use them to help visually during developing and other little pieces of codes that can help you speed your coding.

To use the class just include where needed like so:

```php
require 'directory-composer-is-located/vendor/autoload.php';
use Utils\Utils;

$u = new Utils();
```

## Some Useful Examples

#### Echo Array
Echo array is a quick snipped of code that place an array or object inside a predefined tag, instead of constantly typing the whole code I just use:

```php
$array = ['hello', 'world'];
echo $u->echo_array($array);
```

This will render as:

```html
<pre>
$array = Array(
    'hello',
    'world'
)
</pre>
```

I also recently added `echo_text($string)` which renders a string into a text area `<textarea>` tag.

### Current Site URL
Creating dynamic URLs is very useful for transporting site from one domain to another, instead of using static urls like `https://github.com`. With site_url() method applied to all your links, will maintain this dynamic while redering a static full url. Only returns the main server name or domain.

Example:

```php
use xTags\xTags;
$x = new xTags();

echo $x->p($u->site_url());
echo $x->a('Hello World', array('href' => $u->site_url() . "/hello-world"));
```

Renders:

```html
<!-- link on example.com -->
<p>http://example.com</p>
<a href="https://example.com/hellow-world">Hello World</a>

<!-- same link moved to https://new-example.com -->
<p>https://new-example.com</p>
<a href="https://new-example.com/hello-world">Hello World</a>
```