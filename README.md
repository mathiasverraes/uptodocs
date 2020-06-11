# UpToDocs

UpToDocs scans a Markdown file for PHP code blocks, and execute each one in a sandbox. 

Include this in your CI workflows, to make sure your documentation is always up to date with your code.


## Usage

```
composer require-dev mathiasverraes/uptodocs
```

CLI:

```
vendor/bin/uptodocs run <markdownFile> [<preludeFile>]

Arguments:
  markdownFile  Markdown file to run.
  preludeFile   A PHP file to run before each code block. 
                Useful for imports and other setup code.
```

In your code: 

```php
<?php
$upToDocs = new Verraes\UpToDocs\UpToDocs();
$result = $upToDocs->run("README.md", "prelude.php"); // bool
```

## Example

You can try it on this README file you are reading. 

Run `bin/uptodocs run README.md` and see an error message like this: 

```
The following code block in /Users/mathias/workspace/php/uptodocs/README.md:27 failed.
<?php
$upToDocs = new Verraes\UpToDocs\UpToDocs();
$result = $upToDocs->run("README.md", "prelude.php"); // bool

==================
PHP Fatal error:  Uncaught Error: Class 'Verraes\UpToDocs\UpToDocs' not found in Standard input code:4
Stack trace:
#0 {main}
  thrown in Standard input code on line 4
```

The problem here was that `vendor/autoload.php` wasn't included, but we can fix that by adding the prelude: `bin/uptodocs run README.md prelude.php`. (But don't actually run that, you'll create an infinite loop!)

