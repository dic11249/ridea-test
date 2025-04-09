<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="ArticleStoreRequest",
 *     title="文章請求",
 *     description="文章創建和更新請求的數據結構",
 *     required={"title", "content", "author"},
 *     @OA\Property(
 *         property="title",
 *         type="object",
 *         description="文章標題 (多語言)",
 *         @OA\Property(property="zh", type="string", nullable=true, example="中文標題"),
 *         @OA\Property(property="en", type="string", nullable=true, example="English Title"),
 *         @OA\Property(property="ja", type="string", nullable=true, example="日本語のタイトル")
 *     ),
 *     @OA\Property(
 *         property="content",
 *         type="object",
 *         description="文章內容 (多語言)",
 *         @OA\Property(property="zh", type="string", nullable=true, example="這是中文內容"),
 *         @OA\Property(property="en", type="string", nullable=true, example="This is English content"),
 *         @OA\Property(property="ja", type="string", nullable=true, example="これは日本語のコンテンツです")
 *     ),
 *     @OA\Property(
 *         property="author",
 *         type="string",
 *         description="作者名稱",
 *         maxLength=20,
 *         example="王小明"
 *     )
 * )
 */
class StoreArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'array'],
            'title.zh' => ['string', 'nullable'],
            'title.en' => ['string', 'nullable'],
            'title.ja' => ['string', 'nullable'],
            'content' => ['required', 'array'],
            'content.zh' => ['string', 'nullable'],
            'content.en' => ['string', 'nullable'],
            'content.ja' => ['string', 'nullable'],
            'author' => ['required', 'string', 'min:1', 'max:20'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // 確保至少有一種語言的標題
            if (empty($this->input('title.zh')) && empty($this->input('title.en')) && empty($this->input('title.ja'))) {
                $validator->errors()->add('title', '至少需要提供一種語言的標題');
            }

            // 確保至少有一種語言的內容
            if (empty($this->input('content.zh')) && empty($this->input('content.en')) && empty($this->input('content.ja'))) {
                $validator->errors()->add('content', '至少需要提供一種語言的內容');
            }
        });
    }
}
