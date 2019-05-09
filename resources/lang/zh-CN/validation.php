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

	'accepted'         => '必须接受 [:attribute]。',
	'active_url'       => '[:attribute] 不是一个有效的网址。',
	'after'            => '[:attribute] 必须是一个在 :date 之后的日期。',
	'after_or_equal'   => '[:attribute] 必须是一个大于等于 :date 的日期。',
	'alpha'            => '[:attribute] 只能由字母组成。',
	'alpha_dash'       => '[:attribute] 只能由字母、数字和斜杠组成。',
	'alpha_num'        => '[:attribute] 只能由字母和数字组成。',
	'array'            => '[:attribute] 必须是一个数组。',
	'before'           => '[:attribute] 必须是一个在 :date 之前的日期。',
	'before_or_equal'  => '[:attribute] 必须是一个小于等于 :date 的日期。',
	'between'          => [
		'numeric' => '[:attribute] 必须介于 :min - :max 之间。',
		'file'    => '[:attribute] 必须介于 :min - :max kb 之间。',
		'string'  => '[:attribute] 必须介于 :min - :max 个字符之间。',
		'array'   => '[:attribute] 必须只有 :min - :max 个单元。',
	],
	'boolean'          => '[:attribute] 必须为True/False。',
	'confirmed'        => '[:attribute] 两次输入不一致。',
	'date'             => '[:attribute] 不是一个有效的日期。',
	'date_equals'      => '[:attribute] 值必须等于 :date.',
	'date_format'      => '[:attribute] 的格式必须为 :format。',
	'different'        => '[:attribute] 和 :other 必须不同。',
	'digits'           => '[:attribute] 必须是 :digits 位的数字。',
	'digits_between'   => '[:attribute] 必须是介于 :min 和 :max 位的数字。',
	'dimensions'       => '[:attribute] 图片不符合所要求的尺寸。',
	'distinct'         => '[:attribute] 有重复的值。',
	'email'            => '[:attribute] 不是一个合法的邮箱。',
	'exists'           => '[:attribute] 不存在。',
	'file'             => '[:attribute] 必须是一个有效的文件。',
	'filled'           => '[:attribute] 不能为空。',
	'gt'               => [
		'numeric' => '[:attribute] 值必须大于 :value。',
		'file'    => '[:attribute] 文件大小必须大于 :value KB。',
		'string'  => '[:attribute] 文本长度必须大于 :value 个字符。',
		'array'   => '[:attribute] 数组成员数量必须大于 :value 。',
	],
	'gte'              => [
		'numeric' => '[:attribute] 值必须大于或等于 :value。',
		'file'    => '[:attribute] 文件大小必须大于或等于 :value KB。',
		'string'  => '[:attribute] 文本长度必须大于或等于 :value 个字。',
		'array'   => '[:attribute] 数组成员数量必须大于或等于 :value 。',
	],
	'image'            => '[:attribute] 必须是图片。',
	'in'               => '已选的属性 [:attribute] 非法。',
	'in_array'         => '[:attribute] 必须在 :other 内。',
	'integer'          => '[:attribute] 必须是整数。',
	'ip'               => '[:attribute] 必须是有效的 IP 地址。',
	'ipv4'             => '[:attribute] 必须是有效的 IPv4 地址。',
	'ipv6'             => '[:attribute] 必须是有效的 IPv6 地址。',
	'json'             => '[:attribute] 必须是有效的 JSON 字符串。',
	'lt'               => [
		'numeric' => '[:attribute] 值必须小于 :value。',
		'file'    => '[:attribute] 文件大小必须小于 :value KB。',
		'string'  => '[:attribute] 文本长度必须小于 :value 个字符。',
		'array'   => '[:attribute] 数组成员数量必须小于 :value 。',
	],
	'lte'              => [
		'numeric' => '[:attribute] 值必须小于或等于 :value。',
		'file'    => '[:attribute] 文件大小必须小于或等于 :value KB。',
		'string'  => '[:attribute] 文本长度必须小于或等于 :value 个字符。',
		'array'   => '[:attribute] 数组成员数量必须小于或等于 :value 。',
	],
	'max'              => [
		'numeric' => '[:attribute] 不能大于 :max。',
		'file'    => '[:attribute] 不能大于 :max KB。',
		'string'  => '[:attribute] 不能大于 :max 个字符。',
		'array'   => '[:attribute] 最多只有 :max 个单元。',
	],
	'mimes'            => '[:attribute] 必须是一个 :values 类型的文件。',
	'mimetypes'        => '[:attribute] 必须是一个 :values 类型的文件。',
	'min'              => [
		'numeric' => '[:attribute] 必须大于等于 :min。',
		'file'    => '[:attribute] 大小不能小于 :min KB。',
		'string'  => '[:attribute] 至少为 :min 个字符。',
		'array'   => '[:attribute] 至少有 :min 个单元。',
	],
	'not_in'           => '已选的属性 [:attribute] 非法。',
	'not_regex'        => '[:attribute] 格式不正确。',
	'numeric'          => '[:attribute] 必须是一个数字。',
	'present'          => '[:attribute] 不能为空。',
	'regex'            => '[:attribute] 格式不正确。',
	'required'         => '[:attribute] 不能为空。',
	'required_if'      => '当 :other 为 :value 时 [:attribute] 不能为空。',
	'required_unless'  => '除了 :other 为 :values 时 [:attribute] 都不能为空。',
	'required_with'    => '当 :values 存在时 [:attribute] 不能为空。',
	'required_with_all' => '当 :values 存在时 [:attribute] 不能为空。',
	'required_without' => '当 :values 不存在时 [:attribute] 不能为空。',
	'required_without_all' => '当 :values 都不存在时 [:attribute] 不能为空。',
	'same'             => '[:attribute] 和 :other 必须相同。',
	'size'             => [
		'numeric' => '[:attribute] 值必须为 :size。',
		'file'    => '[:attribute] 文件大小必须为 :size KB。',
		'string'  => '[:attribute] 文本长度必须是 :size 个字符。',
		'array'   => '[:attribute] 数组成员数量必须为 :size 个。',
	],
	'starts_with'      => '[:attribute] 必须以 :values 开始',
	'string'           => '[:attribute] 必须是一个字符串。',
	'timezone'         => '[:attribute] 必须是一个合法的时区值。',
	'unique'           => '[:attribute] 已经存在。',
	'uploaded'         => '[:attribute] 文件上传失败.',
	'url'              => '[:attribute] 格式不正确。',
	'uuid'             => '[:attribute] 必须是一个合法的 UUID.',
	'phone'            => '[:attribute] 格式不正确。',
	'id_card'          => '[:attribute] 格式不正确。',
	'not_zero'         => '[:attribute] 必须有值，或不能为0。',
	'catalog'          => '[:attribute] 必须是 :name 的子分类(Catalog ID)。',
	'catalog_name'     => '[:attribute] 必须是 :name 的子分类(Catalog NAME)。',
	'required_if_catalog' => '当 :other 为 :value 时 [:attribute] 不能为空。',

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => [
		'attribute-name' => [
			'rule-name' => 'custom-message',
		],
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

	'attributes' => [],

];
