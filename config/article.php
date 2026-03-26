<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Draft notice presets
    |--------------------------------------------------------------------------
    |
    | Keys are submitted as draft_reason_preset. Values are stored in
    | articles.draft_reason and shown on the article template when status is draft.
    |
    */
    'draft_reason_presets' => [
        'incomplete' => 'This draft is incomplete; more research or content is needed.',
        'review' => 'This draft is awaiting editorial review before publication.',
        'sources' => 'Sources or references for this draft are still being added.',
        'sensitive' => 'This topic is sensitive and the draft needs further review.',
    ],

];
