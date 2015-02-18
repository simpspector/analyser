configuring projects to use SimpSpector
=======================================

You can tell SimpSpector what tests to run on your project by placing a `.simpspector.yml` configuration file in your project's root folder.

.simpspector.yml configuration file
-----------------------------------

Every **SimpSpector Gadget** posesses a short name, which is needed for activating the gadget. Simple add a key with short name to the yml file.

sample config:

```yml
phpcs:
  files: src/
  standards: [PSR1, PSR2]
phpmd:
  files: src/
function_blacklist:
  files: src/
  blacklist:
    die: notice
    echo: notice
    var_dump: error
comment_blacklist:
  files: src/
  blacklist:
    todo: notice
    "dont commit": error
security-checker:
  directory: ./
  level: critical
```

List of all SimpSpector Gadgets
-------------------------------

* [PHP CodeSniffer (phpcs)](gadgets/phpcs.md)
* [PHP Mess Detector (phpmd)](gadgets/phpmd.md)
* [Function Blacklist (function_blacklist)](gadgets/function_blacklist.md)
* [Security Checker (security-checker)](gadgets/security-checker.md)
* [Twig Syntax Checker (twig-ling)](gadgets/twig-lint.md)


