# BIIGLE Demo Module

Install the module:

Add the following to the repositories array of your `composer.json`:
```
{
  "type": "vcs",
  "url": "git@github.com:BiodataMiningGroup/biigle-demo.git"
}
```

1. Run `php composer.phar require biigle/demo`.
2. Add `'Biigle\Modules\Demo\DemoServiceProvider'` to the `providers` array in `config/app.php`.
3. Run `php artisan demo:config` and open `config/demo.php`. Set the ID of the label tree and volume to attach to each new demo project. If you don't do this, new demo projects will be empty.
