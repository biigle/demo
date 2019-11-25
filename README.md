# BIIGLE Demo Module

This is the BIIGLE module to offer a demo project to new users.

## Installation

1. Run `composer require biigle/demo`.
2. Add `Biigle\Modules\Demo\DemoServiceProvider::class` to the `providers` array in `config/app.php`.
3. Run `php artisan vendor:publish --tag=public` to publish the public assets of this module.
4. In your `.env` file, set `DEMO_LABEL_TREE_ID` to the ID of the label tree, `DEMO_VOLUME_ID` to the ID of the volume and/or `DEMO_VIDEO_ID` to the ID of the video to attach to each new demo project. If you don't do this, new demo projects will be empty. You can also set an optional `DEMO_PROJECT_NAME` (default is "Demo Project").

## Developing

Take a look at the [development guide](https://github.com/biigle/core/blob/master/DEVELOPING.md) of the core repository to get started with the development setup.

Want to develop a new module? Head over to the [biigle/module](https://github.com/biigle/module) template repository.

## Contributions and bug reports

Contributions to BIIGLE are always welcome. Check out the [contribution guide](https://github.com/biigle/core/blob/master/CONTRIBUTING.md) to get started.
