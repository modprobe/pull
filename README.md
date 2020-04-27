# pull

It pulls your repositories. Surprising, I know.

Essentially, it's a slower alternative to [oh-my-repos](https://github.com/utkuufuk/oh-my-repos),
though it does have prettier output and more flexible recursion.

## Setup

I'd suggest you use composer to install it globally:

```bash
$ composer global require alxt/pull
```

Or don't. I'm not your mom. (Or am I? There was that time in the 90s...)

## Config

You can configure it by placing a [NEON file](https://ne-on.org) at
`~/.config/pull/config.neon`. You can override the config path with the 
appropriate command flag.

The config file currently has just two entries:
```neon
projectDir: "~/projects/secret_nuclear_bunker"
maxDepth: 5
```
