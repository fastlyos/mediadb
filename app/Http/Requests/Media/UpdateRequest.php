<?php

namespace App\Http\Requests\Media;

use App\Support\Sanitizer\SlugifyFilter;
use Illuminate\Foundation\Http\FormRequest;
use Waavi\Sanitizer\Laravel\SanitizesInput;

class UpdateRequest extends FormRequest
{
    use SanitizesInput;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'nullable|string|min:1|max:255',
            'description' => 'nullable|string|min:0|max:1024',
            'status' => 'nullable|string|in:private,public',
            'model' => 'nullable|array',
            'snapshot' => 'nullable|numeric|min:0|max:14400',
            'playlists' => 'nullable|array|min:0|max:25',
            'playlists.*' => 'required|array',
            'playlists.*.id' => 'required|string|min:1|max:255',
            'playlists.*.name' => 'required|string|min:1|max:255',
            'tags' => 'nullable|array|min:0|max:15',
            'tags.*' => 'required|array',
            'tags.*.id' => 'required|string|min:1|max:255',
            'tags.*.type' => 'nullable|string|in:genre,language,person',
            'tags.*.name' => 'required|string|min:1|max:255',
        ];
    }

    /**
     *  @return array
     */
    public function filters()
    {
        return [
            'name' => 'trim|strip_tags',
            'description' => 'trim|strip_tags',
            'snapshot' => 'trim|cast:float',
            'status' => 'trim|escape|lowercase',
            'model.id' => 'trim|strip_tags',
            'playlists.*.id' => 'trim|strip_tags',
            'playlists.*.name' => 'trim|strip_tags',
            'tags.*.id' => 'trim|strip_tags',
            'tags.*.type' => 'trim|strip_tags|slug',
            'tags.*.name' => 'trim|strip_tags',
        ];
    }

    /**
     * @return array
     */
    public function customFilters()
    {
        return [
            'slug' => SlugifyFilter::class,
        ];
    }
}
