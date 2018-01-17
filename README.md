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
