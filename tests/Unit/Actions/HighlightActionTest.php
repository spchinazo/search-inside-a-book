<?php

use App\Actions\HighlightAction;
use App\Actions\SearchAction;
use App\Book;
use App\BookPage;

it('should hightlight the content', function () {
    $highLightAction = app(HighlightAction::class)->handle("php", "php content");

    expect($highLightAction)->toBe("<mark>php</mark> content");
});
