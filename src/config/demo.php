<?php

return [

    /*
    | ID of the (public) label tree to attach to each demo project.
    */
    'label_tree_id' => env('DEMO_LABEL_TREE_ID', null),

    /*
    | ID of the image volume to attach to each demo project. The volume is cloned without
    | existing annotations, i.e. each demo project gets its own volume.
    */
    'image_volume_id' => env('DEMO_VOLUME_ID', null),

    /*
    | ID of the video volume to attach to each demo project. The volume is cloned without
    | existing annotations, i.e. each demo project gets its own volume.
    */
    'video_volume_id' => env('DEMO_VIDEO_ID', null),

    /*
    | Name of each new demo project.
    */
    'project_name' => env('DEMO_PROJECT_NAME', 'Demo Project'),

];
