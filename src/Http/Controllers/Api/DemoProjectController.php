<?php

namespace Biigle\Modules\Demo\Http\Controllers\Api;

use Biigle\Role;
use Biigle\Image;
use Biigle\Volume;
use Biigle\Project;
use Biigle\LabelTree;
use Ramsey\Uuid\Uuid;
use Illuminate\Contracts\Auth\Guard;
use Biigle\Http\Controllers\Api\Controller;

class DemoProjectController extends Controller
{
    /**
     * Creates a new demo project which already has a label tree and volume attached
     *
     * @api {post} projects/demo Create a new demo project
     * @apiGroup Projects
     * @apiName StoreProjectDemo
     * @apiPermission user
     * @apiDescription Redirects to the newly created project.
     *
     * @param Guard $auth
     */
    public function store(Guard $auth)
    {
        $user = $auth->user();
        $project = new Project;
        $project->name = 'Demo Project';
        $project->description = "Demo project of {$user->firstname} {$user->lastname}";
        $project->setCreator($user);
        $project->save();

        $tree = LabelTree::publicTrees()->find(config('demo.label_tree_id'));
        if ($tree) {
            $project->labelTrees()->attach($tree);
        }

        $volume = Volume::find(config('demo.volume_id'));
        if ($volume) {
            $newVolume = $volume->replicate();
            $newVolume->save();
            $project->addVolumeId($newVolume->id);

            $images = $volume->images()->pluck('filename')->map(function ($item) use ($newVolume) {
                return [
                    'filename' => $item,
                    'uuid' => Uuid::uuid4(),
                    'volume_id' => $newVolume->id,
                ];
            });

            Image::insert($images->toArray());
            $newVolume->handleNewImages();
        }

        return redirect()->route('project', $project->id);
    }
}
