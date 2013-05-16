ShopwareCLI
===========

ShopwareCLI is not only a growing library of shopware CLI Tools, but a full blown infrastructure framework for adding
feature rich command line applications to your shopware system.

It is build upon the powerful symfony console component, supports management of mulitple shopware instances and ships
with a growing set of administrative task managers and code generators.

> Disclaimer: ShopwareCLI is a fall-out from my daily work with shopware. I'm not releasing it because it's yet stable
> and it's not associated with the shopware AG. I'm releasing, because I'm not comfortable with closed source.

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
    ./sw select foo       # Lists all available commands on *nix systems
```

What's happening now is that ShopwareCLI uses the given shop path to establish a runtime environment for shopware from
the cli - it bootstraps the shopware instance, configures dependencies, establishes a database connections and loads resources
such as doctrine and the zend framework. Upcoming commands will have access to Shopware and Enlight Core functions.

If you want to use another shop, just run the select command again.

> If no shop is selected, ShopwareCLI uses the first one from your list.

You can now play along with the available commands or write your own.

You get help by typing `./sw help [commandname]

Many commands have flags, like: `./sw cache:clear -templates` (clears only the template cache) or have arguments (like
the select command, you have already used).

You can use shortcuts as well:

```Shell
    ./sw plugin:list       # Lists all plugins in your system
    ./sw p:l               # Does the same
```

There are many more features available such as autocompletion and interactive shell for configuration and we plan to use
these for upcoming features such as an extension kickstarter or interactive model generation.

Since we're based on the symfony console, it may be a good start to read their [documentation](symfony.com/doc/2.0/components/console/introduction.html),
it may come in handy, when it comes to write your own commands!

Writing your own commands
-------------------------

This section is under development. Please look at the existing Commands in `Avantgarde\ShopwareCLI\Command` in the meantime.

Licence
-------

ShopwareCLI is published under the [GNU Licence for Free Software](http://www.gnu.org/licenses/gpl.html).

