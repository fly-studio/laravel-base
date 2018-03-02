<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElasticsearch extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!env('SCOUT_DRIVER')) return;
		$e = app('elasticsearch');
		$params = [
			'index' => $e->getConfig('index'),
			'body' => [
				'settings' => [
					'analysis' => [
						'analyzer' => [
							'pinyin_standard' => [ //常规的拼音+汉字搜
								'type' => 'custom',
								'tokenizer' => 't_pinyin',
								'filter' => [
									'unique' //分词之后去重
								],
							],
							'ik_smart_standard' => [ //适合正文
								'type' => 'custom',
								'tokenizer' => 'ik_max_word', //先分词
								'filter' => [
									'unique' //分词之后去重
								],
								'char_filter' => [
									'html_strip' //去除html代码
								]
							],
							'ik_smart_pinyin' => [ //适合标题
								'type' => 'custom',
								'tokenizer' => 'ik_max_word', //先分词
								'filter' => [
									'f_pinyin', 'f_ngram', 'unique' //将分词转化成拼音，并且一律ngram
								],
								'char_filter' => [
									'html_strip' //去除html代码
								]
							],
							'title_standard' => [
								'type' => 'custom',
								'tokenizer' => 'standard',
								'filter' => [
									'lowercase',
									'asciifolding',
									'f_ngram', //为了适应任何字的搜索，将每种组合都来一遍
									'unique',
								]
							],
							'title_search_standard' => [
								'type' => 'custom',
								'tokenizer' => 'standard',
								'filter' => [
									'lowercase', 'asciifolding', 'unique'
								]
							],
							'path' => [
								'type' => 'custom',
								'tokenizer' => 'path_hierarchy'
							],
							'url' => [
								'type' => 'custom',
								'tokenizer' => 'uax_url_email'
							],
							'email' => [
								'type' => 'custom',
								'tokenizer' => 'uax_url_email',
								'filter' => [
									'lowercase', 'unique'
								]
							],
						],
						'tokenizer' => [
							't_pinyin' => [
								'type' => 'pinyin',
								'keep_first_letter' => true, //首拼 ldh
								'keep_separate_first_letter' => false, //拆分首拼成单个 l d h
								'keep_full_pinyin' => false, //全拼(单个) liu de hua
								'keep_joined_full_pinyin' => true, //全拼(合并) liudehua
								'keep_original' => true, //保留原始
								'limit_first_letter_length' => 25, //首拼最长字符长
								'lowercase' => true, //小写
							],
						],
						'filter' => [
							'f_pinyin' => [
								'type' => 'pinyin',
								'keep_first_letter' => true,
								'keep_separate_first_letter' => false,
								'keep_full_pinyin' => false,
								'keep_joined_full_pinyin' => true,
								'keep_original' => true,
								'limit_first_letter_length' => 25,
								'lowercase' => true
							],
							'f_ngram' => [ //主要针对不换行的标题使用，方便模糊搜索
								'type' => 'edgeNGram',
								'min_gram' => 2,
								'max_gram' => 25,
								'side' => 'front',
							],
							'f_word_delimiter' => [ //与 standard 冲突
								'type' => 'word_delimiter',
								'split_on_numerics' => true, //单词中有数字则分割 w3c -> w 3 c
								'split_on_case_change' => true, // 按照大小写分割getAllData -> get All Data
								'generate_word_parts' => true, //按照有意义的单词分割，比如PowerPoint -> Power Point
								'generate_number_parts' => true, //数字被符号分割 111+222 -> 111 222
								'catenate_words' => true, //将 blue-ray 转为 blueray
								'catenate_numbers' => true, //将 139-1234-5678 转为 13912345678
								'catenate_all' => true, //将 uni-corn-110 转为 unicorn110
								'preserve_original' => true, //保留原始的
							],
							/*'f_synonym' => [
								'type' => 'synonym',
								'synonyms_path' => 'analysis/synonym.txt', //同义词路径 相对于config目录
							],*/
						],
					],
				],
				'mappings' => [

					'_default_' => [
						/*'_all' => [
							'enabled' => true
						],*/
						'dynamic_templates' => [
							[
								'keywords' => [
									'match_pattern' => 'regex',
									'match_mapping_type' => 'string',
									'match' => '^.*?_type$', //多态的type 或者是其他string的type类型，全词匹配
									'mapping' => [
										'type' => 'keyword',
									],
								],
							],
							[
								'whole_words' => [
									'match_pattern' => 'regex',
									'match_mapping_type' => 'string',
									'match' => '^(name|username|account)$',
									'mapping' => [
										'type' => 'text',
										'search_analyzer' => 'title_search_standard',
										'analyzer' => 'title_standard',
									],

								],
							],
							[
								'fulltext' => [
									'match_pattern' => 'regex',
									'match_mapping_type' => 'string',
									'match' => '^.*?(content|text|description)$',
									'mapping' => [
										'type' => 'text',
										'search_analyzer' => 'ik_smart_standard',
										'analyzer' => 'ik_smart_standard',
									],
								],
							],
							[
								'pinyin' => [
									'match_pattern' => 'regex',
									'match_mapping_type' => 'string',
									'match' => '^.*?(name|title|alias)$', //realname nickname title subtitle
									'mapping' => [
										'type' => 'text',
										'search_analyzer' => 'pinyin_standard',
										'analyzer' => 'ik_smart_pinyin',
									],
								],
							],
							[
								'phones' => [
									'match_mapping_type' => 'string',
									'match' => '*phone',
									'mapping' => [
										'type' => 'text',
										'search_analyzer' => 'title_search_standard',
										'analyzer' => 'title_standard',
									],
								],
							],
							[
								'dates' => [
									'match_mapping_type' => 'string',
									'match' => '*_at',
									'mapping' => [
										'type' => 'date',
										'format' => 'yyy-MM-dd HH:mm:ss||yyyy-MM-dd||HH:mm:ss'
									],
								],
							],
							[
								'timestamp' => [
									'match_mapping_type' => 'long',
									'match' => '*_at',
									'mapping' => [
										'type' => 'date',
										'format' => 'seconds-since-the-epoch||milliseconds-since-the-epoch',
									],
								],
							],
							[
								'urls' => [
									'match_pattern' => 'regex',
									'match_mapping_type' => 'string',
									'match' => '^.*?(mail|link|url)s?$',
									'mapping' => [
										'type' => 'text',
										'search_analyzer' => 'url',
										'analyzer' => 'url',
									],
								],
							],
							[
								'paths' => [
									'match_pattern' => 'regex',
									'match_mapping_type' => 'string',
									'match' => '^.*?(path)s?$',
									'mapping' => [
										'type' => 'text',
										'analyzer' => 'path',
										//'search_analyzer' => 'path',
									],
								],
							],
							[
								'ips' => [
									'match_pattern' => 'regex',
									'match_mapping_type' => 'string',
									'match' => '^(ip|ip_address|ip_addr)$',
									'mapping' => [
										'type' => 'ip',
									],
								],
							],
							[
								'locations' => [
									'match_pattern' => 'regex',
									'match_mapping_type' => '*',
									'match' => '^(location|point|coordinate)$',
									'mapping' => [
										'type' => 'geo_point',
									],
								],
							],
						],
					],
				],
			],
		];
		if ($e->indices()->exists(array_only($params, 'index')))
			$e->indices()->delete(array_only($params, 'index'));
		$e->indices()->create($params);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

	}
}
