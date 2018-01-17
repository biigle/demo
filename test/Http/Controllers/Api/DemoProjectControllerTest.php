<?php

namespace Biigle\Modules\Demo\Tests\Http\Controllers\Api;

use ApiTestCase;
use Biigle\Role;
use Biigle\Visibility;
use Biigle\Tests\ImageTest;
use Biigle\Tests\LabelTreeTest;

class DemoProjectControllerTest extends ApiTestCase
{
    public function testStoreEmpty()
    {
        $this->doTestApiRoute('POST', '/api/v1/projects/demo');

        $this->beUser();
        $this->assertFalse($this->user()->projects()->exists());
        $this->post('/api/v1/projects/demo')->assertStatus(302);

        $project = $this->user()->projects()->first();
        $this->assertNotNull($project);
        $this->assertContains("{$this->user()->firstname} {$this->user()->lastname}", $project->description);
        $this->assertFalse($project->labelTrees()->exists());
        $this->assertFalse($project->volumes()->exists());
    }

    public function testStoreWithLabelTree()
    {
        $this->beUser();
        $tree = LabelTreeTest::create(['visibility_id' => Visibility::$private->id]);
        // Add member so the label tree is no global label tree and attached by default.
        $tree->addMember($this->editor(), Role::$admin);

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

        $tree->visibility_id = Visibility::$public->id;
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

        $this->expectsJobs(\Biigle\Jobs\GenerateThumbnails::class);
        $this->expectsJobs(\Biigle\Jobs\CollectImageMetaInfo::class);
        $this->post('/api/v1/projects/demo')->assertStatus(302);

        $volume = $this->user()->projects()->first()->volumes()->first();
        $this->assertNotNull($volume);
        $this->assertNotEquals($image->volume_id, $volume->id);
        $this->assertEquals($image->volume->name, $volume->name);
        $this->assertEquals($image->volume->url, $volume->url);
        $this->assertEquals($image->filename, $volume->images()->first()->filename);
    }
}
