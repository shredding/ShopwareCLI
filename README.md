ShopwareCLI
===========

ShopwareCLI is not only a growing library of shopware CLI Tools, but a full blown infrastructure framework for adding
feature rich command line applications to your shopware system.

It is build upon the powerful symfony console component, supports management of mulitple shopware instances and ships
with a growing set of administrative task managers and code generators.

> Disclaimer: ShopwareCLI is a fall-out from my daily work with shopware. I'm not releasing it because it's yet stable
> and it's not associated with the shopware AG. I'm releasing it, because I'm not comfortable with closed source.

ShopwareCLI supports shopware 4.1 and 4.0 with zero-configuration fallbacks.

Installation
------------
> ShopwareCLI requires at least PHP 5.4.

ShopwareCLI is installed via [composer](http://getcomposer.org/). Since we do not have reached stable yet, you have to
select *dev-stable*.

```Shell
    composer create-project avantgarde/shopware-cli shopwareCLI dev-master
```

This will install your ShopwareCLI into a folder named *shopwareCLI*. Inside it, you'll find the following structure:

```
    Avantgarde
        ShopwareCLI
            ...                   # Project Files

    config
        example.config.yml        # A sample configuration file
        phpunit.xml               # Configuration for the test suite
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

Commands are the available commands inside the system. ShopwareCLI is built for writing command line apps for shopware
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

Many commands have flags, like: `./sw cache:clear --templates` (clears only the template cache) or have arguments (like
the select command, you have already used).

You can use shortcuts as well:

```Shell
    ./sw plugin:list       # Lists all plugins in your system
    ./sw p:l               # Does the same
```

There are many more features available such as autocompletion and interactive shell for configuration and I'm planning
to use these for upcoming features such as an extension kickstarter or interactive model generation.

Since we're based on the symfony console, it may be a good start to read it's [documentation](http://symfony.com/doc/current/components/console/introduction.html).
It may as well come in handy, when it comes to write your own commands!

Writing your own commands
-------------------------

Writing your own commands is easy. Once you have read the symfony console documentation, you are almost perfectly prepared
to write command line tools for shopware.

Inside of the commands execute file, you have access to shopwares global functions such as Shopware() and Enlight(), there
is as well an established database connection, plugins are manipulable, doctrine is there and so on. Autoloading is
available as well. You can use large portions of the core code in your own commands.

However, there are a few thing that are uniqure to ShopwareCLI, like you have to register your command to config.yml.

You might as well need information about the shop, such as it's web url or path and you will want to write unit tests for
your commands.

The basic way to do this is to implement the `EnvironmentAwareInterface` in your commands. It will inject everything you need,
as you can see in the Interface footprint.

However, normally you just want to extend `ShopwareCommand` when writing a new command. It's pushed at the end of symfony's
command's inheritance chain and does the implementation for you and gives you some nice shortcuts.

Here are some examples:

```php

class YourCommand extends ShopwareCommand {

    protected function execute(InputInterface $input, OutputInterface $output)

        // Shopinformation
        $this->shop->getName();                 # Name of the shop as configured in config.yml
        $this->shop->getPath();                 # Root path of the configured shop
        $this->shop->getWeb();                  # Web address of the Shop
        $this->shop->getShopwareInstance();     # Accessor to the Shopware() global function
        $this->shop->getEnlightInstance();      # Accessor to the Enlight() global function

        // There are as well some shortcuts
        $this->shop->getRepository($fullyQualifiedModelClassName);
        $this->shop->getDb();

        // Services can be accessed like this:
        $this->getService($serviceName);

        // The configuration is available like this:
        $this->configuration->get('shops');         # Returns all configured shops as array;
        $this->configuration->getBaseDirectory();   # Returns the base directory of shopware CLI
        $this->configuration->getShopByName($name); # Returns a shop by the configured name

        // You can define your own configuration structure in config.yml and access it as
        // array like this:
        $this->configuration->get('your_configuration');

    }

}

```

While you can access `Shopware()` and `Enlight()` from within your commands, you should use ShopwareCLI's shop wrapper,
as available via `$this->shop`. It's a wrapper around Shopwares core functionality and since it's injected into the
command by the application, it's loosly coupled - and makes testing much easier.

ShopwareCLI uses some other symfony components like *filesystem* or *dependency-injection*. If you are not familiar with
these tools, you might want to have a look into the fantastic component documentation, as they are really helping you
to write testable and maintainable command line applications for shopware.


Execute controller actions
--------------------------

ShopwareCLI supports you by writing commands and gives you access to the entire shopware infrastructure.

However, most use cases that are already implemented in shopware, it's plugins or your own plugin, are operated by controllers.
The MVC pattern of  shopware (as in most if not all PHP MVC Frameworks) is tied to the HTTP Request / Response cycle, that
is not available from the command line.

This would force you to reimplement the controller logic again in your commands. That's not DRY, that's not KISS, that's not FUN!

But ShopwareCLI is supposed to be fun! Therefore it ships with an easy-to-use *controller component* that makes it remarkably easy
to execute shopwares controller actions.

In order to make a controller CLI ready, you have to *extend* the original controller within the CLI context like this:

```php
class PluginController extends \Shopware_Controllers_Backend_Plugin {

    use CLIControllerTrait;

    public function __construct(array $pluginInformation) {
        $this->initialize($pluginInformation);
    }

}
```

As you can see, we get away with *very* little code.

A CLI-ready controller must meet two demands:

* it must use `Avantgarde\ShopwareCLI\Controller\CLIControllerTrait`
* it must override the constructor

Behind the scenes, the trait is patching the original *Englight_Action* and exchanges it's core actors - namely *Request* and *View* -
with it's own implementations.

It's not necessary to accept an array in the constructor, but it's good practise to initialize the CLI Controller right away.

`$pluginInformation` becomes the request data, and will be passed to the controller wrapped in a class, masquerading as request.

Let's have a look on an actual implementation, `plugin:deactivate` is a command that makes use of the *PluginController*:

```php
    $pluginName = $input->getArgument('plugin');

    $repository = $this->shop->getRepository('\Shopware\Models\Plugin\Plugin');

    /** @var \Shopware\Models\Plugin\Plugin $plugin */
    $plugin = $repository->findOneBy(['name' => $pluginName]);
    $controller= new PluginController(
        [
            'id'        =>  $plugin->getId(),
            'installed' =>  $plugin->getInstalled(),
            'version'   =>  $plugin->getVersion()
        ]
    );
    $controller->savePluginAction();
```

The to-be-called action is named `savePluginAction` and it needs a few information about the plugin given as array. We pass
them to the constructor and can call the controller action. It's that easy.

The last step to do is printing some output. Controller are *assigning* the output information to the *View* class, and - normal circumstances assumed -
a smarty template or ExtJS will take care of the rendering. We can easily report the action's outcome by retrieving these assignments:


```php
    if ($controller->getAssign()['success']) {
        // Looks like it worked!
    } else {
        // Something went wrong.
    };
```

> The controller component is a fallout from a real-life project and may not yet be masquerading all necessary methods for your prokect.
> But it's build with extendability in mind, so please report missing features to help stabilize it.



Licence
-------

Copyright (c) 2013, Die Digitale Avantgarde UG (haftungsbeschränkt)
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation
and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS
BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

