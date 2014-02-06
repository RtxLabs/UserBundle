About
============

Bundle that provides user functionality for Symfony2 projects. This bundle is provided as a part of the
SmartBusinessPlatform from ROTEX Heating Systems and is most usefull in combination with other bundles of this
platform.

[![Build Status](https://secure.travis-ci.org/RtxLabs/UserBundle.png)](http://travis-ci.org/RtxLabs/UserBundle)

Installation
============

## Installation

### Step 1) Get the bundle

First, grab the RtxLabsUserBundle. There are two different ways
to do this:

#### Method a) Using `composer`

Add the following lines to your `composer.json` file and then run `php composer.phar install`:

```
    "rtxlabs/user-bundle": "dev-master"
```

#### Method b) Using submodules

Run the following commands to bring in the needed libraries as submodules.

```bash
git submodule add https://github.com/RtxLabs/UserBundle.git vendor/bundles/RtxLabs/UserBundle
```

### Step 2) Register the bundle

To start using the bundle, register it in your Kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new RtxLabs\UserBundle\RtxLabsUserBundle(),
    );
    // ...
)
```

Usage
============

```

TODO
============

* Write a decent documentation
* Add unit tests