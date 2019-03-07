<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 20.06.18
 * Time: 11:48
 */

namespace App\Models\Api\Posts;
use App\Models\TargetConfig;

interface PostInterface
{
    public static function track();

    public static function renderView($params = []);

    public static function createDocument();

    public static function editDocument();

    public static function deleteDocument();

    public static function editView(TargetConfig $integration);

    public static function otherFieldsView($params = []);
}