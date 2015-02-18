Function Blacklist Gadget (function_blacklist)
==============================================

This gadget checks for blacklisted functions and adds issues for every occurence. The error level can be configured for every function individually.

**parameters**

* files
    * which files should be inspected, relative to the projects root folder.
    * only files matching '*.php' will be considered
    * defaults to '.'
* blacklist
    * hash map
    * keys = function name
    * values = error level triggered
    * default: **TODO**

**IMPORTANT** currently no distinction can be made between `die` and `exit`, therefore listing one of these in the blacklist parameter will result in issues being created for occurences of both functions. As a result, different error levels are also not possible.

configuration snippet

```yml
function_blacklist:
  files: src/
  blacklist:
    die: notice
    echo: notice
    var_dump: error
```

