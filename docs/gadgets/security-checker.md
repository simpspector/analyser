Security Checker
================

This gadget checks your composer.lock if you have installed packages with security vulnerabilities. 

**parameters**

* directory
    * which folder contains the composer.lock, relative to the projects root folder.
    * defaults to './'
* level
    * error level assigned to issues created by this gadget
    * possible values: notice, warning, error or critical
    * default: critical

configuration snippet

```yml
security-checker:
  director: ./
  level: critical
```