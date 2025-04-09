<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Article",
 *     title="Article",
 *     description="文章模型",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="文章 ID"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="object",
 *         description="文章標題 (多語言)",
 *         @OA\Property(property="zh", type="string", example="中文標題"),
 *         @OA\Property(property="en", type="string", example="English Title"),
 *         @OA\Property(property="ja", type="string", example="日本語のタイトル")
 *     ),
 *     @OA\Property(
 *         property="content",
 *         type="object",
 *         description="文章內容 (多語言)",
 *         @OA\Property(property="zh", type="string", example="這是中文內容"),
 *         @OA\Property(property="en", type="string", example="This is English content"),
 *         @OA\Property(property="ja", type="string", example="これは日本語のコンテンツです")
 *     ),
 *     @OA\Property(
 *         property="author",
 *         type="string",
 *         maxLength=20,
 *         description="作者名稱",
 *         example="王小明"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="建立時間"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="更新時間"
 *     )
 * )
 */
class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'author'
    ];

    protected $casts = [
        'title' => 'array',
        'content' => 'array',
    ];
}
