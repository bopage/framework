<?php

use App\Blog\BlogWidget;

use function DI\get;

return [
    'blog.prefix' => '/blog',
    'admin.widgets' => [
        get(BlogWidget::class)
    ]
];
