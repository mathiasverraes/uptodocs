# Uptodocs

UpToDocs scans a Markdown file for PHP code blocks, and execute each one in a sandbox. 

Include this in your CI workflows, to make sure your documentation is always up to date with your code.

## Usage

```
uptcode run <markdownFile> [<preludeFile>]

Arguments:
  markdownFile  Markdown file to run
  preludeFile   A PHP file to run before each code block. 
                Useful for imports and other setup code.
```

