# Rollup plugin for CakePHP

This plugin it's not really about the code, it just gives you a structure how to handle CSS/JS, and some helper method.

_SEO is not a problem ?_ You want a full client-side rendered frontend ? Then maybe you should check [inertiajs](https://inertiajs.com/).
_SEO or SSR is not a problem ?_ You want to use CakePHP as a backend and build the entire frontend in JS ? Then you should check [vuejs](https://vuejs.org/).
You want just an 'assetcompressor' ? Then you should check [markstory/asset_compress](https://github.com/markstory/asset_compress)

## Installation

You can install this plugin into your CakePHP application using [composer](https://getcomposer.org).

The recommended way to install composer packages is:

```
composer require bor-attila/cakephp-rollup:dev-master
```

Enable the plugin in your __Application.php__:

```
$this->addPlugin('CakephpRollup');
```

As package manager. I will use `yarn`, but you can use `npm` if you want.

Create a __package.json__ in the _webroot_ directory with _yarn_ if you don't have one.

```
cd webroot
yarn init
```

Create the default folder structure and create the basic files.

```
bin/cake rollup:init
```

After you successfully executed this command, your WEBROOT directory should look like this.

```
+-- css
+-- scss
|   +-- style.scss
+-- plugins
+-- js
|   +-- src
|       +-- components
|       +-- mixins
|       +-- static
|           +-- script.js
|       +-- main.app.js
+-- babel.config.json
+-- rollup.config.js
+-- packages.json
```

The `css` folder contains the compiled stylesheets. You can add this line to gitignore

```webroot/css/*.min.css```

The `scss` folder contains the stylesheet source code.
The `plugins` folder contains static production ready third party libraries (eg. bootstrap, axios, select2).
The `js` folder contains the compiled javascript files. You can add this line to gitignore

```webroot/js/*.min.js```

The `js/src` folder contains javascript app(!) source files - vue, react etc... Eg: x.app.js, y.app.js.
The `js/src/components` folder contains javascript app components source files.
The `js/src/mixins` folder contains javascript reusable components.
The `js/src/static` folder contains javascript source code that can be included directly into page ('old way').

## Working with stylesheets

### Install dependencies:

You will need DartSDK installed!

```
yarn add dart-sass
```

Add these scripts into your _package.json_

```
"scripts": {
    "rollup:scss:build": "`../bin/cake rollup:sass`",
    "rollup:scss:watch": "`../bin/cake rollup:sass -w`"
    "rollup:scss:clean": "rm -f css/*.min.css"
}
```

### SCSS commands

#### Production

When you run `yarn rollup:scss:build` all sass files from scss folder __which starts with a letter__ (^[a-zA-Z]) will be compiled into
css and minimized.

For example:
 * __scss/style.scss__ -> __css/style.min.css__
 * __scss/mystyle.scss__ -> __css/mystyle.min.css__
 * __scss/\_variables.scss__ remains untouched (ofc if you included in your __style.scss__ then will be compiled)

#### Development

When you run `yarn rollup:scss:watch` all sass files from scss folder __which starts with a letter__ (^[a-zA-Z]) will be compiled into
css and the sass compiler will listen to file changes.

### Helpers

#### CSS helper

In _View\AppView.php_ add this to the _initialize_ method for expl:

```php
$this->loadHelper('CakephpRollup.Css');
```

OR

```php
$this->loadHelper('CakephpRollup.Css', [
    'storage' => [
        'body' => ['bodyclass'],
    ]
]);
```

The CSS helper is just an array manipulation. In the container you can store class names.

```
add(string $container, string $class, ?string $overwrite = null): bool
remove(string $container, string $class): bool
has(string $container, string $class): bool
get(string $container): string
```

```
<html>
    <head>
    </head>
    <body <?= $this->Css->get(); ?>>

        //In the tempalte
        $this->Css->add('body', 'green');

        //Or conditionally
        if (true) {
            $this->Css->add('body', 'red', 'green');// the green will be replaced with red
            $this->Css->remove('body', 'red');// or remove
        }

    </body>
</html>
```

#### StyleSheet helper

In _View\AppView.php_ add this to the _initialize_ method:

```php
$this->loadHelper('CakephpRollup.Stylesheet', [
    'cache' => 'default'
]);
```

The __StyleSheet helper__ helps to load CSS file content and inject it directly into the body.
These methods search for specified CSS files, opens, creates a style tag and stores it into cache (if it's set).

```
global(array $stylesheets = []): string
```
Returns the global stylesheet's content. Automatically searches for the css/style[.hash]?[.min]?.css
You can add more CSS files as parameter.

```
local(): string
```
Returns the local stylesheet's content. Automatically searches for:
 * css/{prefix}-{controller}-{action}[.hash]?[.min]?.css
 * css/{controller}-{action}[.hash]?[.min]?.css if there is no prefix.

```
inline(string $name): string
```
Returns the given stylesheet's content. Automatically searches for the css/{name}[.hash]?[.min]?.css

## Working with Javascript

Install dependencies:

```
yarn add rollup rollup-plugin-terser @rollup/plugin-node-resolve @rollup/plugin-commonjs @rollup/plugin-json
yarn add core-js@3 @babel/core @babel/preset-env @rollup/plugin-babel
```

Add these scripts into your _package.json_

```
"scripts": {
    "rollup:js:build": "rollup -c",
    "rollup:js:watch": "rollup -c --w",
    "rollup:js:clean": "rm -f js/*.min.js"
}
```

### Helpers

#### Javascript helper

In _View\AppView.php_ add this to the _initialize_ method:

```php
$this->loadHelper('CakephpRollup.Javascript', [
    'cache' => 'default'
]);
```

How to use:

```
<html>
    <head>
    </head>
    <body>
        ....
        <?= $this->Html->script($this->Javascript->files('main', 'debug', 'awesome')); ?>
        <!--
            This returns
            <script src="js/main.laknsdn78t7f34t79.min.js" />
            <script src="js/debug.sanibiobrevoybowueb.min.js" />
            <script src="js/awesome.wqojndiqwd766686.min.js" />
            or even
            <script src="js/main.min.js" />
            <script src="js/awesome.min.js" />
        -->
    </body>
</html>
```
