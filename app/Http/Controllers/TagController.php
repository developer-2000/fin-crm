<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends BaseController
{
    public function findTag(Request $request){

        $tags = (new Tag)->findByWordTag(trim($request->get('query')), $request->tag_name, $request->partner_id);

        $result = [];

        if ($tags->isNotEmpty()) {
            foreach ($tags as $tag) {
                $result[] = [
                    'id'   => $tag->id,
                    'text' => $tag->value
                ];
            }
        }

        return response()->json($result);
    }
}
