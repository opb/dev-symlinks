# Instructions

Add the following to the `extras` section of your project's `composer.json`:

```json
    "extra": {
        "dev-symlink-envs": [
            "dev",
            "development"
        ]
    }
```
_The `extras` section itself is included for clarity._

This will go through any configured `repositories` in your `composer.json` of type `path` and if the current environment, as defined by the `APP_ENV` environment variable, is in the list defined, then the composer config will be change to `"symlink": true`.

For example:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../path/to/local/package",
            "options": {
                "symlink": false
            }
        }
    ],
    "require": {
        "vendor/package": "*"
    },
    "extra": {
        "dev-symlink-envs": [
            "dev",
            "development"
        ]
    }
}
```

In this case, whenever `APP_ENV` is either `dev` or `development` then the `symlink` option for the local package will be set to `true`.