# Frequently Asked Questions

## Common Issues

### The debugbar is not showing up

1. Check if it's enabled in your environment:
```php
'enabled' => env('DEBUGBAR_ENABLED', null)
```

2. Verify your environment settings
3. Check if you're in production mode

### Performance Impact

**Q: Will the debugbar slow down my application?**

A: The debugbar does add some overhead. That's why it's recommended to:
- Disable it in production
- Only enable needed collectors
- Use the file storage driver for larger datasets

### Storage Issues

**Q: Where is the data stored?**

A: By default, debugbar data is stored in:
```
storage/debugbar
```

You can change this in the configuration file.

## Best Practices

1. Always disable in production
2. Clear storage regularly
3. Only enable needed collectors
4. Use middleware to control access

## Getting Help

- Check the [GitHub Issues](https://github.com/barryvdh/laravel-debugbar/issues)
- Join Laravel community channels
- Review the source code