<?php

namespace App\Http\Requests\Classwall;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class PostCommentAddRequest extends FormRequest
{
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
        Validator::extend('check_comments',function ($attribute,$value,$parameters,$validatior)
        {
            return preg_match('/[A-Za-z0-9_~\-!@#\$%\^&*.,:(\)\s\x{1F600}-\x{1F64F}]/u', request('comments')); 
        });

        $rules = [
            //
            'comments'      =>  'nullable|check_comments|required_without:attachment',
            'attachment'    =>  'nullable|max:2048|mimes:jpg,jpeg,png|required_without:comments',
        ];

        return $rules;
    }

    public function messages()
    {
        $messages = [
            //
            'comments.check_comments'       =>  'Enter Valid Comments',
            'comments.required_without'     =>  'Enter either comments or select attachment',

            'attachment.mimes'              =>  "Attachment should be 'JPG or PNG'",
            'attachment.max'                =>  'Attachment size should be within 2MB',
            'attachment.required_without'   =>  'Enter either comments or select attachment',
        ];

        return $messages;
    }
}