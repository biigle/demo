<?php

namespace Biigle\Modules\Demo\Http\Controllers\Api;

use Biigle\Http\Controllers\Api\Controller;
use Biigle\Jobs\CreateNewImagesOrVideos;
use Biigle\LabelTree;
use Biigle\Project;
use Biigle\Volume;
use Illuminate\Contracts\Auth\Guard;

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

        $volume = Volume::find(config('demo.image_volume_id'));
        if ($volume) {
            $this->replicateVolume($volume, $project, $user);
        }

        $volume = Volume::find(config('demo.video_volume_id'));
        if ($volume) {
            $this->replicateVolume($volume, $project, $user);
        }

        return redirect()->route('project', $project->id);
    }

    /**
     * Replicate a volume.
     *
     * @param Volume $volume
     * @param Project $project
     * @param User $creator
     */
    protected function replicateVolume($volume, $project, $creator)
    {
        $newVolume = $volume->replicate();
        $newVolume->creator()->associate($creator);
        $newVolume->save();
        $project->addVolumeId($newVolume->id);
        $files = $volume->files()->pluck('filename')->toArray();

        (new CreateNewImagesOrVideos($newVolume, $files))->handle();
    }
}
