<?php

namespace Biigle\Modules\Demo\Http\Controllers\Api;

use Biigle\Role;
use Biigle\Volume;
use Biigle\Project;
use Biigle\LabelTree;
use Biigle\Jobs\CreateNewImages;
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
        $this->authorize('create', Project::class);

        $project = new Project;
        $project->name = config('demo.project_name');
        $project->description = "Demo project of {$user->firstname} {$user->lastname}";
        $project->creator()->associate($user);
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
            $images = $volume->images()->pluck('filename')->toArray();

            (new CreateNewImages($newVolume, $images))->handle();
        }

        return redirect()->route('project', $project->id);
    }
}
