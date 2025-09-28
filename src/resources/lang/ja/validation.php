<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attributeを承認してください。',
    'accepted_if' => ':otherが:valueの場合、:attributeを承認してください。',
    'active_url' => ':attributeは有効なURLではありません。',
    'after' => ':attributeは:dateより後の日付にしてください。',
    'after_or_equal' => ':attributeは:date以降の日付にしてください。',
    'alpha' => ':attributeは英字のみで入力してください。',
    'alpha_dash' => ':attributeは英数字・ハイフン・アンダースコアで入力してください。',
    'alpha_num' => ':attributeは英数字で入力してください。',
    'array' => ':attributeは配列で入力してください。',
    'ascii' => ':attributeは英数字と記号のみで入力してください。',
    'before' => ':attributeは:dateより前の日付にしてください。',
    'before_or_equal' => ':attributeは:date以前の日付にしてください。',
    'between' => [
        'array' => ':attributeは:min個から:max個で入力してください。',
        'file' => ':attributeは:minKBから:maxKBのサイズで入力してください。',
        'numeric' => ':attributeは:minから:maxの間で入力してください。',
        'string' => ':attributeは:min文字から:max文字で入力してください。',
    ],
    'boolean' => ':attributeは真偽値で入力してください。',
    'can_login' => '提供された認証情報でログインできません。',
    'current_password' => 'パスワードが正しくありません。',
    'date' => ':attributeは正しい日付で入力してください。',
    'date_equals' => ':attributeは:dateと等しい日付で入力してください。',
    'date_format' => ':attributeは:format形式で入力してください。',
    'decimal' => ':attributeは:decimal桁で入力してください。',
    'declined' => ':attributeを拒否してください。',
    'declined_if' => ':otherが:valueの場合、:attributeを拒否してください。',
    'different' => ':attributeと:otherは異なる値で入力してください。',
    'digits' => ':attributeは:digits桁で入力してください。',
    'digits_between' => ':attributeは:min桁から:max桁で入力してください。',
    'dimensions' => ':attributeの画像サイズが正しくありません。',
    'distinct' => ':attributeに重複した値があります。',
    'doesnt_end_with' => ':attributeは:valuesで終わらない値で入力してください。',
    'doesnt_start_with' => ':attributeは:valuesで始まらない値で入力してください。',
    'ends_with' => ':attributeは:valuesで終わる値で入力してください。',
    'enum' => '選択された:attributeは正しくありません。',
    'exists' => '選択された:attributeは正しくありません。',
    'extensions' => ':attributeは以下の拡張子のいずれかで入力してください: :extensions。',
    'failed' => '認証に失敗しました。',
    'file' => ':attributeはファイルで入力してください。',
    'filled' => ':attributeは必須項目です。',
    'gt' => [
        'array' => ':attributeは:value個より多く入力してください。',
        'file' => ':attributeは:valueKBより大きいサイズで入力してください。',
        'numeric' => ':attributeは:valueより大きい値で入力してください。',
        'string' => ':attributeは:value文字より多く入力してください。',
    ],
    'gte' => [
        'array' => ':attributeは:value個以上で入力してください',
        'file' => ':attributeは:valueKB以上のサイズで入力してください',
        'numeric' => ':attributeは:value以上の値で入力してください',
        'string' => ':attributeは:value文字以上で入力してください',
    ],
    'hex_color' => ':attributeは有効な16進色コードで入力してください。',
    'image' => ':attributeは画像で入力してください。',
    'in' => '選択された:attributeは正しくありません。',
    'in_array' => ':attributeは:otherに存在しません。',
    'integer' => ':attributeは整数で入力してください。',
    'ip' => ':attributeは正しいIPアドレスで入力してください。',
    'ipv4' => ':attributeは正しいIPv4アドレスで入力してください。',
    'ipv6' => ':attributeは正しいIPv6アドレスで入力してください。',
    'json' => ':attributeは正しいJSON形式で入力してください。',
    'lowercase' => ':attributeは小文字で入力してください。',
    'lt' => [
        'array' => ':attributeは:value個より少なく入力してください。',
        'file' => ':attributeは:valueKBより小さいサイズで入力してください。',
        'numeric' => ':attributeは:valueより小さい値で入力してください。',
        'string' => ':attributeは:value文字より少なく入力してください。',
    ],
    'lte' => [
        'array' => ':attributeは:value個以下で入力してください。',
        'file' => ':attributeは:valueKB以下のサイズで入力してください。',
        'numeric' => ':attributeは:value以下の値で入力してください。',
        'string' => ':attributeは:value文字以下で入力してください。',
    ],
    'mac_address' => ':attributeは正しいMACアドレスで入力してください。',
    'max_digits' => ':attributeは:max桁以下で入力してください。',
    'mimes' => ':attributeは:valuesタイプのファイルで入力してください。',
    'mimetypes' => ':attributeは:valuesタイプのファイルで入力してください。',
    'min_digits' => ':attributeは:min桁以上で入力してください。',
    'missing' => ':attributeが存在しません。',
    'missing_if' => ':otherが:valueの場合、:attributeが存在しません。',
    'missing_unless' => ':otherが:valueでない場合、:attributeが存在しません。',
    'missing_with' => ':valuesが存在する場合、:attributeが存在しません。',
    'missing_with_all' => ':valuesがすべて存在する場合、:attributeが存在しません。',
    'multiple_of' => ':attributeは:valueの倍数で入力してください。',
    'next' => '次のページに進んでください。',
    'not_in' => '選択された:attributeは正しくありません。',
    'not_regex' => ':attributeの形式が正しくありません。',
    'numeric' => ':attributeは数値で入力してください。',
    'password' => [
        'letters' => ':attributeには少なくとも1つの文字が含まれている必要があります。',
        'mixed' => ':attributeには少なくとも1つの大文字と1つの小文字が含まれている必要があります。',
        'numbers' => ':attributeには少なくとも1つの数字が含まれている必要があります。',
        'symbols' => ':attributeには少なくとも1つの記号が含まれている必要があります。',
        'uncompromised' => '指定された:attributeがデータ漏洩に含まれています。別の:attributeを選択してください。',
    ],
    'present' => ':attributeが存在する必要があります。',
    'present_if' => ':otherが:valueの場合、:attributeが存在する必要があります。',
    'present_unless' => ':otherが:valueでない場合、:attributeが存在する必要があります。',
    'present_with' => ':valuesが存在する場合、:attributeが存在する必要があります。',
    'present_with_all' => ':valuesがすべて存在する場合、:attributeが存在する必要があります。',
    'previous' => '前のページに戻ってください。',
    'prohibited' => ':attributeフィールドは禁止されています。',
    'prohibited_if' => ':otherが:valueの場合、:attributeフィールドは禁止されています。',
    'prohibited_unless' => ':otherが:valuesに含まれていない場合、:attributeフィールドは禁止されています。',
    'prohibits' => ':attributeフィールドは:otherの存在を禁止しています。',
    'regex' => ':attributeの形式が正しくありません。',
    'required_array_keys' => ':attributeフィールドには以下のエントリが必要です: :values。',
    'required_if' => ':otherが:valueの場合、:attributeは必須項目です。',
    'required_if_accepted' => ':otherが承認された場合、:attributeは必須項目です。',
    'required_unless' => ':otherが:valuesに含まれていない場合、:attributeは必須項目です。',
    'required_with' => ':valuesが存在する場合、:attributeは必須項目です。',
    'required_with_all' => ':valuesがすべて存在する場合、:attributeは必須項目です。',
    'required_without' => ':valuesが存在しない場合、:attributeは必須項目です。',
    'required_without_all' => ':valuesがすべて存在しない場合、:attributeは必須項目です。',
    'size' => [
        'array' => ':attributeは:size個で入力してください。',
        'file' => ':attributeは:sizeKBのサイズで入力してください。',
        'numeric' => ':attributeは:sizeで入力してください。',
        'string' => ':attributeは:size文字で入力してください。',
    ],
    'starts_with' => ':attributeは:valuesで始まる値で入力してください。',
    'timezone' => ':attributeは正しいタイムゾーンで入力してください。',
    'uploaded' => ':attributeのアップロードに失敗しました。',
    'uppercase' => ':attributeは大文字で入力してください。',
    'url' => ':attributeは正しいURLで入力してください。',
    'ulid' => ':attributeは有効なULIDで入力してください。',
    'uuid' => ':attributeは有効なUUIDで入力してください。',

    // 今回利用しているエラーメッセージ
    'required' => ':attributeを入力してください',
    'string' => ':attributeは文字列で入力してください',
    'email' => ':attributeの形式が不正です',
    'confirmed' => ':attributeと一致しません',
    'min' => [
        // 'array' => ':attributeは:min個以上で入力してください。',
        // 'file' => ':attributeは:minKB以上のサイズで入力してください。',
        // 'numeric' => ':attributeは:min以上の値で入力してください。',
        'string' => ':attributeは:min文字以上で入力してください',
    ],
    'max' => [
        // 'array' => ':attributeは:max個以下で入力してください。',
        // 'file' => ':attributeは:maxKB以下のサイズで入力してください。',
        // 'numeric' => ':attributeは:max以下の値で入力してください。',
        'string' => ':attributeは:max文字以下で入力してください',
    ],
    'same' => 'パスワードと一致しません',
    'unique' => 'この:attributeは既に使用されています',


    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        // 特別なカスタマイズが必要な場合のみここに追加
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        // 属性名は各FormRequestで個別に定義するため、ここでは空にしておく
    ],

];
