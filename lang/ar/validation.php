<?php

return [

    /*
    |--------------------------------------------------------------------------
    | رسائل التحقق
    |--------------------------------------------------------------------------
    |
    | الرسائل التالية هي الرسائل الافتراضية لرسائل الخطأ التي يستخدمها مُحقّق البيانات.
    |
    */

    'accepted'             => 'يجب قبول الحقل :attribute.',
    'active_url'           => 'الحقل :attribute ليس رابطاً صالحاً.',
    'after'                => 'يجب أن يكون الحقل :attribute تاريخاً بعد :date.',
    'after_or_equal'       => 'يجب أن يكون الحقل :attribute تاريخاً بعد أو يساوي :date.',
    'alpha'                => 'يجب أن يحتوي الحقل :attribute على أحرف فقط.',
    'alpha_dash'           => 'يجب أن يحتوي الحقل :attribute على أحرف وأرقام وشرطات وشرطات سفلية فقط.',
    'alpha_num'            => 'يجب أن يحتوي الحقل :attribute على أحرف وأرقام فقط.',
    'array'                => 'يجب أن يكون الحقل :attribute مصفوفة.',
    'before'               => 'يجب أن يكون الحقل :attribute تاريخاً قبل :date.',
    'before_or_equal'      => 'يجب أن يكون الحقل :attribute تاريخاً قبل أو يساوي :date.',
    'between'              => [
        'numeric' => 'يجب أن يكون الحقل :attribute بين :min و :max.',
        'file'    => 'يجب أن يكون الحقل :attribute بين :min و :max كيلوبايت.',
        'string'  => 'يجب أن يكون الحقل :attribute بين :min و :max حرفاً.',
        'array'   => 'يجب أن يحتوي الحقل :attribute بين :min و :max عنصراً.',
    ],
    'boolean'              => 'يجب أن يكون الحقل :attribute إما true أو false.',
    'confirmed'            => 'تأكيد الحقل :attribute غير مطابق.',
    'date'                 => 'الحقل :attribute ليس تاريخاً صالحاً.',
    'date_format'          => 'لا يطابق الحقل :attribute الصيغة :format.',
    'different'            => 'يجب أن يكون الحقلان :attribute و :other مختلفين.',
    'digits'               => 'يجب أن يكون الحقل :attribute :digits رقماً.',
    'digits_between'       => 'يجب أن يكون الحقل :attribute بين :min و :max رقماً.',
    'dimensions'           => 'الحقل :attribute له أبعاد صورة غير صالحة.',
    'distinct'             => 'للحقل :attribute قيمة مكررة.',
    'email'                => 'يجب أن يكون الحقل :attribute عنوان بريد إلكتروني صالحاً.',
    'exists'               => 'الحقل :attribute المحدد غير صالح.',
    'file'                 => 'يجب أن يكون الحقل :attribute ملفاً.',
    'filled'               => 'يجب أن يحتوي الحقل :attribute على قيمة.',
    'gt'                   => [
        'numeric' => 'يجب أن يكون الحقل :attribute أكبر من :value.',
        'file'    => 'يجب أن يكون الحقل :attribute أكبر من :value كيلوبايت.',
        'string'  => 'يجب أن يكون الحقل :attribute أكبر من :value حرفاً.',
        'array'   => 'يجب أن يحتوي الحقل :attribute أكثر من :value عنصراً.',
    ],
    'gte'                  => [
        'numeric' => 'يجب أن يكون الحقل :attribute أكبر من أو يساوي :value.',
        'file'    => 'يجب أن يكون الحقل :attribute أكبر من أو يساوي :value كيلوبايت.',
        'string'  => 'يجب أن يكون الحقل :attribute أكبر من أو يساوي :value حرفاً.',
        'array'   => 'يجب أن يحتوي الحقل :attribute :value عنصراً أو أكثر.',
    ],
    'image'                => 'يجب أن يكون الحقل :attribute صورة.',
    'in'                   => 'الحقل :attribute المحدد غير صالح.',
    'in_array'             => 'الحقل :attribute غير موجود في :other.',
    'integer'              => 'يجب أن يكون الحقل :attribute عدداً صحيحاً.',
    'ip'                   => 'يجب أن يكون الحقل :attribute عنوان IP صالحاً.',
    'ipv4'                 => 'يجب أن يكون الحقل :attribute عنوان IPv4 صالحاً.',
    'ipv6'                 => 'يجب أن يكون الحقل :attribute عنوان IPv6 صالحاً.',
    'json'                => 'يجب أن يكون الحقل :attribute نص JSON صالحاً.',
    'lt'                   => [
        'numeric' => 'يجب أن يكون الحقل :attribute أصغر من :value.',
        'file'    => 'يجب أن يكون الحقل :attribute أصغر من :value كيلوبايت.',
        'string'  => 'يجب أن يكون الحقل :attribute أصغر من :value حرفاً.',
        'array'   => 'يجب أن يحتوي الحقل :attribute أقل من :value عنصراً.',
    ],
    'lte'                  => [
        'numeric' => 'يجب أن يكون الحقل :attribute أصغر من أو يساوي :value.',
        'file'    => 'يجب أن يكون الحقل :attribute أصغر من أو يساوي :value كيلوبايت.',
        'string'  => 'يجب أن يكون الحقل :attribute أصغر من أو يساوي :value حرفاً.',
        'array'   => 'يجب ألا يحتوي الحقل :attribute أكثر من :value عنصراً.',
    ],
    'max'                  => [
        'numeric' => 'يجب ألا يكون الحقل :attribute أكبر من :max.',
        'file'    => 'يجب ألا يكون الحقل :attribute أكبر من :max كيلوبايت.',
        'string'  => 'يجب ألا يكون الحقل :attribute أكبر من :max حرفاً.',
        'array'   => 'يجب ألا يحتوي الحقل :attribute أكثر من :max عنصراً.',
    ],
    'mimes'                => 'يجب أن يكون الحقل :attribute ملفاً من نوع: :values.',
    'mimetypes'            => 'يجب أن يكون الحقل :attribute ملفاً من نوع: :values.',
    'min'                  => [
        'numeric' => 'يجب أن يكون الحقل :attribute على الأقل :min.',
        'file'    => 'يجب أن يكون الحقل :attribute على الأقل :min كيلوبايت.',
        'string'  => 'يجب أن يكون الحقل :attribute على الأقل :min حرفاً.',
        'array'   => 'يجب أن يحتوي الحقل :attribute على الأقل :min عنصراً.',
    ],
    'not_in'               => 'الحقل :attribute المحدد غير صالح.',
    'not_regex'            => 'صيغة الحقل :attribute غير صالحة.',
    'numeric'              => 'يجب أن يكون الحقل :attribute رقماً.',
    'present'              => 'يجب أن يكون الحقل :attribute موجوداً.',
    'regex'                => 'صيغة الحقل :attribute غير صالحة.',
    'required'             => 'الحقل :attribute مطلوب.',
    'required_if'          => 'الحقل :attribute مطلوب عندما يكون :other هو :value.',
    'required_unless'      => 'الحقل :attribute مطلوب إلا إذا كان :other في :values.',
    'required_with'        => 'الحقل :attribute مطلوب عندما يكون :values موجوداً.',
    'required_with_all'    => 'الحقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_without'     => 'الحقل :attribute مطلوب عندما لا يكون :values موجوداً.',
    'required_without_all' => 'الحقل :attribute مطلوب عندما لا يكون أي من :values موجودة.',
    'same'                 => 'يجب أن يتطابق الحقلان :attribute و :other.',
    'size'                 => [
        'numeric' => 'يجب أن يكون الحقل :attribute بحجم :size.',
        'file'    => 'يجب أن يكون الحقل :attribute بحجم :size كيلوبايت.',
        'string'  => 'يجب أن يكون الحقل :attribute بحجم :size حرفاً.',
        'array'   => 'يجب أن يحتوي الحقل :attribute على :size عنصراً.',
    ],
    'string'               => 'يجب أن يكون الحقل :attribute نصاً.',
    'timezone'             => 'يجب أن يكون الحقل :attribute منطقة زمنية صالحة.',
    'unique'               => 'تم استخدام الحقل :attribute بالفعل.',
    'uploaded'             => 'فشل تحميل الحقل :attribute.',
    'url'                  => 'صيغة الحقل :attribute غير صالحة.',

    /*
    |--------------------------------------------------------------------------
    | رسائل تحقق مخصصة
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'رسالة مخصصة',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | أسماء الحقول المخصصة
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'email'    => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'name'     => 'الاسم',
        'username' => 'اسم المستخدم',
    ],

];
