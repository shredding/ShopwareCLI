ShopwareCLI
===========

ShopwareCLI is not only a growing library of shopware CLI Tools, but a full blown infrastructure framework for adding
feature rich command line applications to your shopware system.

It is build upon the powerful symfony console component, supports management of mulitple shopware instances and ships
with a growing set of administrative task managers and code generators.

> Disclaimer: ShopwareCLI is a fall-out from my daily work with shopware. I'm not releasing it because it's yet stable
> and it's not associated with the shopware AG. I'm releasing it, because I'm not comfortable with closed source.

Installation
------------

ShopwareCLI is installed via [composer](http://getcomposer.org/). Since we do not have reached stable yet, you have to
select *dev-stable*.

```Shell
    composer create-project avantgarde/shopware-cli shopwareCLI dev-master
```

This will install your ShopwareCLI into a folder named *shopwareCLI*, inside it, you'll find the following structure:

```
    Avantgarde
        ShopwareCLI
            ...                   # Project Files

    config
        example.config.yml        # A sample configuration file
        phpunit.xml.dist          # Configuration for the test suite
        services.yml              # Dependency Injection Configuration

    tmp                           # An internal directory for caching
    vendor                        # Dependencies

    composer.json                 # Dependency Definitions
    README.md                     # This file
    sw                            # The ShopwareCLI exectuable
```

Rename and open the `example.config.yml` to `config.yml`. There are two trees, to be configured: *shops* and *commands*.

ShopwareCLI can be configured with multiple shop instances - as this is what you normally have in a development environment.
If you mount a remote shop into your local drive you can even control remote shops via command line.

Commands are the the available commands inside the system. ShopwareCLI is built for writing command line apps for shopware
with ease, so you can add your own commands and are ready to use the entire infrastructe.

> It's recommended to create a dedicated composer package for your commands and add them as dependency to composer.json,
> as you won't need to configure the autoloader.
>
> However, it's a fantastic idea to put your commands under tests and do a pull requests for ShopwareCLI, as I'm happy to
> integrate them if they may be of interest for other developers.

That's it.

```Shell
    ./sw        # Lists all available commands on *nix systems
    php sw      # Same on windows, use that in all later examples
```

Now select a shop. If you have named your shop *foo* in the config.yml, it's as easy as:

```Shell
    ./sw select foo
```

What's happening now is that ShopwareCLI uses the given shop path to establish a runtime environment for shopware from
the cli - it bootstraps the shopware instance (of course with bypassing the FrontController), configures dependencies,
establishes a database connection and loads resources such as doctrine and the zend framework.
Upcoming commands will have access to Shopware and Enlight Core functions.

If you want to use another shop, just run the select command again.

> If no shop is selected, ShopwareCLI uses the first one from your list.

You can now play along with the available commands or write your own.

You get help by typing `./sw help [commandname]`.

Many commands have flags, like: `./sw cache:clear -templates` (clears only the template cache) or have arguments (like
the select command, you have already used).

You can use shortcuts as well:

```Shell
    ./sw plugin:list       # Lists all plugins in your system
    ./sw p:l               # Does the same
```

There are many more features available such as autocompletion and interactive shell for configuration and I'm planning
to use these for upcoming features such as an extension kickstarter or interactive model generation.

Since we're based on the symfony console, it may be a good start to read it's [documentation](symfony.com/doc/2.0/components/console/introduction.html).
It may as well come in handy, when it comes to write your own commands!

Writing your own commands
-------------------------

Writing your own commands is easy. Once you have read the symfony console documentation, you are almost perfectly prepared
to write command line tools for shopware.

However, there are a few thing that are uniqure to ShopwareCLI, like you have to register your command to config.yml.

You might as well need information about the shop, such as it's web url or path. Hence, your commands should implement
the `ConfigurationAwareInterface`. ShopwareCLI will then inject a `ConfigurationProvider` instance into your command and
you can use it to access important parameters.

Here are some examples:

```php
    $configurationProvider->getShopName();             # return 'foo'
    $configurationProvider->getShop();                 # returns an array with the shop informations
    $configurationProvider->get('shops');              # returns all shops
    $configurationProvider->get('your_own_config');    # returns the your_own_config from the config.yml as array
    $configurationProvider->getService('filesystem');  # returns an instance of a service as configured in service.yml
```

Inside of the commands execute file, you have access to shopwares global functions such as Shopware() and Enlight(), there
is as well an established database connections, plugins are manipulable, doctrine is there and so on. Autoloading is
available as well.

While you can access `Shopware()` and `Enlight()` from within your commands, you should use the service container as it's
arranging loose coupling and makes testing much easier:

```php

    /** @var \Shopware $shopware */
    $shopware = $configurationProvider->getService('shopware');
```


Licence
-------

ShopwareCLI is published under the [GNU Licence for Free Software](http://www.gnu.org/licenses/gpl.html).

