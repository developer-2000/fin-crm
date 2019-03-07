<?php

Route::get('view/{groupKey?}', [
    'as'         => 'translation-get-view-group-key',
    'uses'       => 'TranslationController@getView',
])->where('groupKey', '.*')
    ->middleware('can:translation_group');

Route::get('/{groupKey?}', [
    'as'         => 'translation-get-index',
    'uses'       => 'TranslationController@getIndex',
])->where('groupKey', '.*')
    ->middleware('can:translation_index');

Route::post('/add/{groupKey}', [
    'as'   => 'translation-post-add',
    'uses' => 'TranslationController@postAdd',
])->where('groupKey', '.*')
    ->middleware('can:translation_words_add');

Route::post('/edit/{groupKey}', [
    'as'   => 'translation-post-edit',
    'uses' => 'TranslationController@postEdit',
])->where('groupKey', '.*')
    ->middleware('can:translation_edit');

Route::post('/groups/add', [
    'as'   => 'translation-post-add-group',
    'uses' => 'TranslationController@postAddGroup',
])->middleware('can:translation_group_add');

Route::post('/delete/{groupKey}/{translationKey}', [
    'as'   => 'translation-post-delete',
    'uses' => 'TranslationController@postDelete',
])->where('groupKey', '.*')
    ->middleware('can:translation_delete_word');

Route::post('/import', [
    'as'   => 'translation-post-import',
    'uses' => 'TranslationController@postImport',
])->middleware('can:translation_import');

Route::post('/find', [
    'as'   => 'translation-post-find',
    'uses' => 'TranslationController@postFind',
])->middleware('can:translation_find');

Route::post('/locales/add', [
    'as'   => 'translation-post-add-locale',
    'uses' => 'TranslationController@postAddLocale',
])->middleware('can:translation_locale_add');

Route::post('/locales/remove', [
    'as'   => 'translation-post-remove-locale',
    'uses' => 'TranslationController@postRemoveLocale',
])->middleware('can:translation_locale_remove');

Route::post('/publish/{groupKey}', [
    'as'   => 'translation-post-publish',
    'uses' => 'TranslationController@postPublish',
])->where('groupKey', '.*')
    ->middleware('can:translation_publish');
