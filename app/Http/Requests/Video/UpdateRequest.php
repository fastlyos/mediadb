<?php

namespace App\Http\Requests\Video;

use Elegant\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    use SanitizesInput;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:255',
            'overview' => 'nullable|string|min:0|max:1024',
            'status' => 'required|string|in:private,public',
            'tags' => 'nullable|array|min:0|max:15',
            'tags.*.type' => 'nullable|string|in:actor,genre,language,studio',
            'tags.*.name' => 'required|string|min:1|max:255',
        ];
    }

    /**
     *  @return array
     */
    public function filters(): array
    {
        return [
            'name' => 'trim|strip_tags',
            'overview' => 'trim|strip_tags',
            'status' => 'trim|escape|lowercase',
            'tags.*.id' => 'trim|strip_tags',
            'tags.*.type' => 'trim|strip_tags|slug',
            'tags.*.name' => 'trim|strip_tags',
        ];
    }
}
