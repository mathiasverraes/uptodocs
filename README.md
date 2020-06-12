# UpToDocs

UpToDocs scans a Markdown file for PHP code blocks, and executes each one in a separate process.

Include this in your CI workflows, to make sure your documentation is always up to date with your code.


## Usage

```
composer require --dev mathiasverraes/uptodocs
```

CLI:

```
vendor/bin/uptodocs run [options] [--] <markdownFile>

Arguments:
  markdownFile          Markdown file to run.

Options:
  -b, --before=BEFORE   A PHP file to run before each code block. 
                        Useful for imports and other setup code.
  -a, --after=AFTER     A PHP file to run after each code block. 
                        Useful for cleanup, and for running assertions.
```

In your code: 

```php
<?php
$upToDocs = new Verraes\UpToDocs\UpToDocs();
$result = $upToDocs->run("README.md"); // bool
```

## Example

You can try it on the Markdown file in the sample directory:

```
./uptodocs run sample/docs.md --before sample/before.php
```
                                                
Output:
 
```
The following code block in /Users/mathias/workspace/php/uptodocs/sample/docs.md:16 failed.
<?php
$v = multiplyy(10,2);

PHP Fatal error:  Uncaught Error: Call to undefined function multiplyy() in Standard input code:11
Stack trace:
#0 {main}
  thrown in Standard input code on line 11
```

UpToDocs discovered a typo in our sample code. Oops!