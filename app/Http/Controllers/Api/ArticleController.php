<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;

/**
 * @OA\Info(
 *     title="文章管理 API",
 *     version="1.0.0",
 *     description="提供新增、刪除、查詢文章的 RESTful API"
 * )
 */
class ArticleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/articles",
     *     summary="獲取所有文章列表",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="頁碼",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="每頁數量",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *          name="title",
     *          in="query",
     *          description="文章標題（模糊搜尋）",
     *          required=false,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *          name="author",
     *          in="query",
     *          description="作者（模糊搜尋）",
     *          required=false,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功獲取文章列表",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Article")
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", example="http://localhost/api/articles?page=1"),
     *                 @OA\Property(property="last", type="string", example="http://localhost/api/articles?page=5"),
     *                 @OA\Property(property="prev", type="string", example=null),
     *                 @OA\Property(property="next", type="string", example="http://localhost/api/articles?page=2")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="links", type="array", 
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="url", type="string", nullable=true),
     *                         @OA\Property(property="label", type="string"),
     *                         @OA\Property(property="active", type="boolean")
     *                     )
     *                 ),
     *                 @OA\Property(property="path", type="string", example="http://localhost/api/articles"),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="to", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=75)
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $lang = $request->get('lang', 'zh'); // 預設語言為 zh

        $query = Article::query();

        if ($request->filled('title')) {
            $query->where("title->{$lang}", 'like', '%' . $request->title . '%');
        }

        if ($request->filled('author')) {
            $query->where('author', 'like', '%' . $request->author . '%');
        }
        $articles = $query->paginate();

        return ArticleResource::collection($articles);
    }

    /**
     * @OA\Post(
     *     path="/api/articles",
     *     summary="新增一篇文章",
     *     tags={"Articles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ArticleStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="文章新增成功",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="驗證錯誤"
     *     )
     * )
     */
    public function store(StoreArticleRequest $request)
    {
        $validated = $request->validated();

        $article = Article::create($validated);

        return new ArticleResource($article);
    }

    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     summary="獲取指定文章",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="文章 ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功獲取文章",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="文章不存在"
     *     )
     * )
     */
    public function show(string $id)
    {
        $article = Article::findOrFail($id);

        return new ArticleResource($article);
    }

    /**
     * @OA\Put(
     *     path="/api/articles/{id}",
     *     summary="更新指定文章",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="文章 ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ArticleUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="文章更新成功",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="文章不存在"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="驗證錯誤"
     *     )
     * )
     */
    public function update(UpdateArticleRequest $request, string $id)
    {
        $article = Article::findOrFail($id);

        $validated = $request->validated();

        $article->update($validated);

        return new ArticleResource($article);
    }

    /**
     * @OA\Delete(
     *     path="/api/articles/{id}",
     *     summary="刪除指定文章",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="文章 ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="文章刪除成功"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="文章不存在"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return response()->json(null, 204);
    }
}
