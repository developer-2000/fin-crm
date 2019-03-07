<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 20.06.18
 * Time: 11:17
 */

namespace App\Models\Api\Posts;


abstract class AbstractPost  implements PostInterface
{
    /**
     * @var bool define create, edit, delete methods for document
     */
    const CREATE = false;
    const EDIT = false;
    const DELETE = false;

    const PRINT_REGISTRY = false;
}