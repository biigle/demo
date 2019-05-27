<?php

namespace Biigle\Modules\Demo\Http\Controllers\Api;

use Queue;
use Biigle\Role;
use Biigle\Volume;
use Biigle\Project;
use Biigle\LabelTree;
use Ramsey\Uuid\Uuid;
use Biigle\Jobs\CreateNewImages;
use Biigle\Modules\Videos\Video;
use Illuminate\Contracts\Auth\Guard;
use Biigle\Http\Controllers\Api\Controller;
use Biigle\Modules\Videos\Jobs\ProcessNewVideo;

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

        if (class_exists(Video::class)) {
            $video = Video::find(config('demo.video_id'));
            if ($video) {
                $newVideo = $video->replicate();
                $newVideo->project_id = $project->id;
                $newVideo->uuid = Uuid::uuid4();
                $newVideo->save();
                Queue::push(new ProcessNewVideo($newVideo));
            }
        }

        return redirect()->route('project', $project->id);
    }
}
