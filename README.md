# Simply framework
[Simply Framework](https://amorfx.github.io/simply-documentation/) is a **WordPress framework** that give you the Symfony dependency injection component and Twig php template engine.

Simply is not a framework that will lose you if you have been developing for some time on WordPress.

It will allow you to save development time by offering you dependency injection as well as the automation of recurring tasks on WordPress.

## Installation
All installation and documentation is [available here](https://amorfx.github.io/simply-documentation/getting-started/installation). Read it carefully.

## Philosophy

The framework follows several important points in order not to complicate the development:
- do not modify the overall structure of WordPress
- avoid adding a large overlay which would make plugin or theme development more complex
- simplify added WordPress things with automated process (post type, taxonomy...)
- use Object-oriented programming

This is why the framework was developed as a WordPress must use plugin

## Cache

The compiled container and Twig templates are cached in `SIMPLY_CACHE_DIRECTORY`, in a sub-directory named after a fingerprint of what they are built from: installed Composer packages, configuration directories, registered plugins and theme, and `WP_DEBUG`. Updating a package, switching theme or adding a configuration directory therefore rebuilds the cache automatically. Register plugins, theme and configuration directories the same way for WP-CLI and web requests, otherwise each one rebuilds the cache of the other.

With `WP_DEBUG` set to `false`, files are not checked for changes. When you deploy code changes that Composer does not see (your theme or plugin services, YAML configuration, Twig templates), clear the cache as part of the deployment:

    php bin/console simply:cache:clear

## Development team
The framework was created by [Clément Décou](https://www.clement-decou.fr), who continues to lead the development.

## Contributing
Any help is appreciated. The project is open-source and i encourage you to participate. You can contribute to the project in multiple ways by:

- Reporting a bug issue
- Suggesting features
- Sending a pull request
- Add a star in [GitHub](https://github.com/Amorfx/simply-framework)
- Following me on [Twitter](https://twitter.com/Amorfx3)
- Sharing the project around you


