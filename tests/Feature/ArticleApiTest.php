<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * 測試獲取文章列表
     */
    public function test_cat_get_all_articles()
    {
        // 建立三篇測試文章
        Article::factory()->count(3)->create();

        // 發送請求並確認回應
        $response = $this->getJson('/api/articles');

        // 檢查狀態碼和資料格式
        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'content', 'author', 'created_at', 'updated_at']
                ],
                'links',
                'meta'
            ]);
    }

    /**
     * 測試獲取單一文章
     */
    public function test_can_get_single_article()
    {
        // 建立文章
        $article = Article::factory()->create();

        // 發送請求
        $response = $this->getJson("/api/articles/{$article->id}");

        // 檢查狀態碼和資料
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $article->id,
                    'title' => $article->title,
                    'content' => $article->content,
                    'author' => $article->author,
                ]
            ]);
    }

    /**
     * 測試獲取不存在的文章
     */
    public function test_cannot_get_nonexistent_article()
    {
        // 發送請求存取不存在的文章
        $response = $this->getJson("/api/articles/999");

        // 應該回傳 404
        $response->assertStatus(404);
    }

    /**
     * 測試新增文章
     */
    public function test_can_create_article()
    {
        $articleData = [
            'title' => [
                'zh' => '中文測試標題',
                'en' => 'English Test Title',
                'ja' => '日本語テストタイトル'
            ],
            'content' => [
                'zh' => '這是中文測試內容',
                'en' => 'This is English test content',
                'ja' => 'これは日本語のテスト内容です'
            ],
            'author' => '測試作者'
        ];

        // 發送請求
        $response = $this->postJson('/api/articles', $articleData);

        // 檢查狀態碼和回應內容
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title' => $articleData['title'],
                    'content' => $articleData['content'],
                    'author' => $articleData['author'],
                ]
            ]);

        // 檢查資料庫是否有該筆資料
        $this->assertDatabaseHas('articles', [
            'author' => $articleData['author']
        ]);
    }

    /**
     * 測試新增僅有部分語言的文章
     */
    public function test_can_create_article_with_partial_languages()
    {
        $articleData = [
            'title' => [
                'zh' => '只有中文標題',
                'en' => 'Only English and Chinese titles'
            ],
            'content' => [
                'zh' => '只有中文內容',
                'en' => 'Only English and Chinese contents'
            ],
            'author' => '測試作者'
        ];

        // 發送請求
        $response = $this->postJson('/api/articles', $articleData);

        // 檢查狀態碼和回應內容
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title' => $articleData['title'],
                    'content' => $articleData['content'],
                ]
            ]);

        // 確認資料庫中只有設定的語言
        $article = Article::find($response->json('data.id'));
        $this->assertArrayHasKey('zh', $article->title);
        $this->assertArrayHasKey('en', $article->title);
        $this->assertArrayNotHasKey('ja', $article->title);
    }

    /**
     * 測試新增文章時缺少必填欄位
     */
    public function test_cannot_create_article_without_required_fields()
    {
        // 缺少 title
        $response = $this->postJson('/api/articles', [
            'content' => [
                'zh' => '這是內容'
            ],
            'author' => '作者'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);

        // 缺少 content
        $response = $this->postJson('/api/articles', [
            'title' => [
                'zh' => '這是標題'
            ],
            'author' => '作者'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content']);
    }

    /**
     * 測試更新文章
     */
    public function test_can_update_article()
    {
        // 先建立一篇文章
        $article = Article::factory()->create();

        $updatedData = [
            'title' => [
                'zh' => '已更新的中文標題',
                'en' => 'Updated English Title',
                'ja' => '更新された日本語タイトル'
            ],
            'content' => [
                'zh' => '已更新的中文內容',
                'en' => 'Updated English Content',
                'ja' => '更新された日本語の内容'
            ],
            'author' => '新作者'
        ];

        // 發送更新請求
        $response = $this->putJson("/api/articles/{$article->id}", $updatedData);

        // 檢查回應
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => $updatedData['title'],
                    'content' => $updatedData['content'],
                    'author' => $updatedData['author']
                ]
            ]);

        // 檢查資料庫是否已更新
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'author' => $updatedData['author']
        ]);
    }

    /**
     * 測試更新不存在的文章
     */
    public function test_cannot_update_nonexistent_article()
    {
        $updatedData = [
            'title' => [
                'zh' => '已更新的中文標題'
            ],
            'content' => [
                'zh' => '已更新的中文內容'
            ],
            'author' => '新作者'
        ];

        // 發送更新請求到不存在的文章
        $response = $this->putJson('/api/articles/999', $updatedData);

        // 應該回傳 404
        $response->assertStatus(404);
    }

    /**
     * 測試刪除文章
     */
    public function test_can_delete_article()
    {
        // 先建立一篇文章
        $article = Article::factory()->create();

        // 發送刪除請求
        $response = $this->deleteJson("/api/articles/{$article->id}");

        // 檢查回應
        $response->assertStatus(204);

        // 確認資料庫中已刪除
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    /**
     * 測試刪除不存在的文章
     */
    public function test_cannot_delete_nonexistent_article()
    {
        // 發送刪除請求到不存在的文章
        $response = $this->deleteJson('/api/articles/999');

        // 應該回傳 404
        $response->assertStatus(404);
    }
}
