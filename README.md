# Topaz

Topaz is a WIP blog engine built with PHP 7.

## Requirements

Topaz requires the following:

- PHP 7.4
- A MySQL/MariaDB database
- The TypeScript compiler (`tsc`)
- A SASS compiler

## Building

To build, first ensure that the `tsc` and `sass` programs are available in your environment. Then, simply run
`./build.sh` in the root directory of the repository. An archive will be generated in `build/` containing the root file
structure of the website. This archive can be extracted to the document root of the web server/virtual host.

## Configuration

### Web Server Configuration

Topaz requires that a configuration file in JSON format be present in order to run. The path to this file must be
specified by the `TOPAZ_CONFIG` environment variable. A sample config file can be found
[in the `res` directory](./res/topaz_config.json) of this repository.

To set the config environment variable in Apache:

```
<VirtualHost>
    ...
    SetEnv TOPAZ_CONFIG /path/to/topaz_config.json
    ...
</VirtualHost>
```

Additionally, your web server should be configured to deny access to the `_internal` directory. To do this in Apache
2.4:

```
<VirtualHost>
    ...
    <Directory /path/to/document_root/_internal/>
        Require all denied
        AllowOverride None
    </Directory>
    ...
</VirtualHost>
```

### Database Configuration

Finally, you must initialize the database for use with Topaz. Create a new database (the name doesn't matter) and run
the [`full.sql` script](./res/database/full.sql) located in the `res/database` directory of this repository. This script
will initialize all tables required by Topaz. Future updates may alter the database structure, in which case patch
scripts will be added to the same directory.

## Developing

For convenience, a `watch.sh` script has been provided in the root of the repository. This script will run continuously
and compile TypeScript and SCSS sources automatically. Note that this script will not create a distribution archive, and
is restricted to compiling these sources.

## Planned Features

Below is a non-exhaustive list of features I would like to implement in Topaz. This list may change with time and not
all listed features will necessarily be implemented.

- Persistent user session cookies
- Cross-request config caching
- On-demand registration token generation
- User management console
- Categories / category management page

## License

Topaz is made available under the [MIT License](./LICENSE). You may use the provided software within its bounds.
