<?php

namespace Biigle\Modules\Demo\Tests\Http\Controllers\Api;

use Queue;
use ApiTestCase;
use Biigle\Role;
use Biigle\Visibility;
use Biigle\Tests\ImageTest;
use Biigle\Tests\LabelTreeTest;
use Biigle\Modules\Videos\Video;
use Biigle\Tests\Modules\Videos\VideoTest;

class DemoProjectControllerTest extends ApiTestCase
{
    public function testStoreEmpty()
    {
        config(['demo.project_name' => 'My demo project']);
        $this->doTestApiRoute('POST', '/api/v1/projects/demo');

        $this->beUser();
        $this->assertFalse($this->user()->projects()->exists());
        $this->post('/api/v1/projects/demo')->assertStatus(302);

        $project = $this->user()->projects()->first();
        $this->assertNotNull($project);
        $this->assertEquals('My demo project', $project->name);
        $this->assertContains("{$this->user()->firstname} {$this->user()->lastname}", $project->description);
        $this->assertFalse($project->labelTrees()->exists());
        $this->assertFalse($project->volumes()->exists());
    }

    public function testStoreWithLabelTree()
    {
        $this->beUser();
        $tree = LabelTreeTest::create(['visibility_id' => Visibility::privateId()]);
        // Add member so the label tree is no global label tree and attached by default.
        $tree->addMember($this->editor(), Role::admin());

        config(['demo.label_tree_id' => 9999]);
        $this->post('/api/v1/projects/demo')->assertStatus(302);

        $project = $this->user()->projects()->first();
        // Label tree does not exist.
        $this->assertFalse($project->labelTrees()->exists());
        $project->delete();

        config(['demo.label_tree_id' => $tree->id]);
        $this->post('/api/v1/projects/demo')->assertStatus(302);

        $project = $this->user()->projects()->first();
        // Label tree is private.
        $this->assertFalse($project->labelTrees()->exists());
        $project->delete();

        $tree->visibility_id = Visibility::publicId();
        $tree->save();
        config(['demo.label_tree_id' => $tree->id]);
        $this->post('/api/v1/projects/demo')->assertStatus(302);

        $project = $this->user()->projects()->first();
        $this->assertEquals($tree->id, $project->labelTrees()->first()->id);
    }

    public function testStoreWithVolume()
    {
        $this->beUser();
        $image = ImageTest::create();
        config(['demo.volume_id' => 999]);

        $this->post('/api/v1/projects/demo')->assertStatus(302);
        // Volume does not exist.
        $project = $this->user()->projects()->first();
        $this->assertNull($project->volumes()->first());
        $project->delete();

        config(['demo.volume_id' => $image->volume_id]);

        Queue::fake();
        $this->post('/api/v1/projects/demo')->assertStatus(302);
        Queue::assertPushed(\Biigle\Jobs\ProcessNewImages::class);

        $volume = $this->user()->projects()->first()->volumes()->first();
        $this->assertNotNull($volume);
        $this->assertNotEquals($image->volume_id, $volume->id);
        $this->assertEquals($image->volume->name, $volume->name);
        $this->assertEquals($image->volume->url, $volume->url);
        $this->assertEquals($image->filename, $volume->images()->first()->filename);
        $this->assertEquals($volume->creator_id, $this->user()->id);
    }

    public function testStoreGuest()
    {
        $this->beUser();
        $this->user()->role_id = Role::guestId();
        $this->user()->save();
        $this->post('/api/v1/projects/demo')->assertStatus(403);
        $this->assertFalse($this->user()->projects()->exists());
    }

    public function testStoreWithVideo()
    {
        if (!class_exists(VideoTest::class)) {
            $this->markTestSkipped('Required the biigle/videos module.');
        }

        $this->beUser();
        $video = VideoTest::create();
        config(['demo.video_id' => 999]);

        $this->post('/api/v1/projects/demo')->assertStatus(302);
        // Video does not exist.
        $project = $this->user()->projects()->first();
        $this->assertFalse(Video::where('project_id', $project->id)->exists());
        $project->delete();

        config(['demo.video_id' => $video->id]);

        Queue::fake();
        $this->post('/api/v1/projects/demo')->assertStatus(302);
        Queue::assertPushed(\Biigle\Modules\Videos\Jobs\ProcessNewVideo::class);

        $project = $this->user()->projects()->first();
        $demoVideo = Video::where('project_id', $project->id)->first();
        $this->assertNotNull($video);
        $this->assertNotEquals($video->id, $demoVideo->id);
        $this->assertEquals($video->name, $demoVideo->name);
        $this->assertEquals($video->url, $demoVideo->url);
    }
}
