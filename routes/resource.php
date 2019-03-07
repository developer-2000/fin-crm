<?php

  Route::get('/download/documentation/{path}', 'Resource\DocumentationController')->name('resource.documentation')->where('path', '(.*)');
