# NYCO WP GTFS Import (beta)

This WordPress Plugin will download GTFS Static data feeds and import them into a WordPress database. Optionally, you can download the static data into a custom directory and import them using another large file importer such as [WP All Import](http://www.wpallimport.com/).

## Features
* Downloads multiple GTFS Static data feeds.
* Unarchives zipped GTFS feed directories as organized folders or flattened file structure.
* Optional import of specific GTFS feed fields.
* Optionally only download (and not import into the database) the static data to a custom directory. This allows the use of an importer that can handle larger files such as [WP All Import](http://www.wpallimport.com/). Currently, this importer may timeout for large file imports.

## Usage

### Installation

It uses composer/installers to install it to the plugin directly using Composer. Just run:

```
composer require nyco/wp-gtfs-import
```

You can also download the package and add it manually to your plugin directory.

# About NYCO

NYC Opportunity is the [New York City Mayor's Office for Economic Opportunity](http://nyc.gov/opportunity). We are committed to sharing open source software that we use in our products. Feel free to ask questions and share feedback. Follow @nycopportunity on [Github](https://github.com/orgs/CityOfNewYork/teams/nycopportunity), [Twitter](https://twitter.com/nycopportunity), [Facebook](https://www.facebook.com/NYCOpportunity/), and [Instagram](https://www.instagram.com/nycopportunity/).