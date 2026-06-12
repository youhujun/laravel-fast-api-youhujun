<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-07-24 14:49:06
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-06-12 16:52:41
 * @FilePath: \youhu-laravel-api-13\database\seeders\LaravelFastApi\System\RegionSeeder.php
 */
namespace Database\Seeders\LaravelFastApi\System;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

use App\Models\LaravelFastApi\V1\System\Region\Region;

use Illuminate\Support\Facades\Config;


class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$regionTreeArray = [
				[
					'region_name' => '北京',
					'region_area' => '华北',
					'children' => 
					[
						[
							'region_name' => '北京市',
							'region_area' => '',
							'children' => 
							[
							
							[
								'region_name' => '东城区',
								'region_area' => '',
							],
							
							[
								'region_name' => '西城区',
								'region_area' => '',
							],
							
							[
								'region_name' => '朝阳区',
								'region_area' => '',
							],
							
							[
								'region_name' => '丰台区',
								'region_area' => '',
							],
							
							[
								'region_name' => '石景山区',
								'region_area' => '',
							],
							
							[
								'region_name' => '海淀区',
								'region_area' => '',
							],
							
							[
								'region_name' => '门头沟区',
								'region_area' => '',
							],
							
							[
								'region_name' => '房山区',
								'region_area' => '',
							],
							
							[
								'region_name' => '通州区',
								'region_area' => '',
							],
							
							[
								'region_name' => '顺义区',
								'region_area' => '',
							],
							
							[
								'region_name' => '昌平区',
								'region_area' => '',
							],
							
							[
								'region_name' => '大兴区',
								'region_area' => '',
							],
							
							[
								'region_name' => '怀柔区',
								'region_area' => '',
							],
							
							[
								'region_name' => '平谷区',
								'region_area' => '',
							],
							
							[
								'region_name' => '密云县',
								'region_area' => '',
							],
							
							[
								'region_name' => '延庆县',
								'region_area' => '',
							],
							
							[
								'region_name' => '其他',
								'region_area' => '',
							],
							],
						],
					],
				],
				[
					'region_name' => '天津',
					'region_area' => '华北',
					'children' => 
					[
						[
							'region_name' => '天津市',
							'region_area' => '',
							'children' => 
							[
							
							[
								'region_name' => '和平区',
								'region_area' => '',
							],
							
							[
								'region_name' => '河东区',
								'region_area' => '',
							],
							
							[
								'region_name' => '河西区',
								'region_area' => '',
							],
							
							[
								'region_name' => '南开区',
								'region_area' => '',
							],
							
							[
								'region_name' => '河北区',
								'region_area' => '',
							],
							
							[
								'region_name' => '红桥区',
								'region_area' => '',
							],
							
							[
								'region_name' => '塘沽区',
								'region_area' => '',
							],
							
							[
								'region_name' => '东丽区',
								'region_area' => '',
							],
							
							[
								'region_name' => '西青区',
								'region_area' => '',
							],
							
							[
								'region_name' => '津南区',
								'region_area' => '',
							],
							
							[
								'region_name' => '北辰区',
								'region_area' => '',
							],
							
							[
								'region_name' => '武清区',
								'region_area' => '',
							],
							
							[
								'region_name' => '宝坻区',
								'region_area' => '',
							],
							
							[
								'region_name' => '宁河县',
								'region_area' => '',
							],
							
							[
								'region_name' => '静海县',
								'region_area' => '',
							],
							
							[
								'region_name' => '蓟县',
								'region_area' => '',
							],
							],
						],
					],
				],
				
				[
					'region_name' => '河北',
					'region_area' => '华北',
					'children' => 
					[
					
					[
						'region_name' => '石家庄市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '井陉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '井陉矿区',
							'region_area' => '',
						],
						
						[
							'region_name' => '元氏县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新乐市',
							'region_area' => '',
						],
						
						[
							'region_name' => '新华区',
							'region_area' => '',
						],
						
						[
							'region_name' => '无极县',
							'region_area' => '',
						],
						
						[
							'region_name' => '晋州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '栾城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '桥东区',
							'region_area' => '',
						],
						
						[
							'region_name' => '桥西区',
							'region_area' => '',
						],
						
						[
							'region_name' => '正定县',
							'region_area' => '',
						],
						
						[
							'region_name' => '深泽县',
							'region_area' => '',
						],
						
						[
							'region_name' => '灵寿县',
							'region_area' => '',
						],
						
						[
							'region_name' => '藁城市',
							'region_area' => '',
						],
						
						[
							'region_name' => '行唐县',
							'region_area' => '',
						],
						
						[
							'region_name' => '裕华区',
							'region_area' => '',
						],
						
						[
							'region_name' => '赞皇县',
							'region_area' => '',
						],
						
						[
							'region_name' => '赵县',
							'region_area' => '',
						],
						
						[
							'region_name' => '辛集市',
							'region_area' => '',
						],
						
						[
							'region_name' => '长安区',
							'region_area' => '',
						],
						
						[
							'region_name' => '高邑县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鹿泉市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '唐山市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '丰南区',
							'region_area' => '',
						],
						
						[
							'region_name' => '丰润区',
							'region_area' => '',
						],
						
						[
							'region_name' => '乐亭县',
							'region_area' => '',
						],
						
						[
							'region_name' => '古冶区',
							'region_area' => '',
						],
						
						[
							'region_name' => '唐海县',
							'region_area' => '',
						],
						
						[
							'region_name' => '开平区',
							'region_area' => '',
						],
						
						[
							'region_name' => '滦南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '滦县',
							'region_area' => '',
						],
						
						[
							'region_name' => '玉田县',
							'region_area' => '',
						],
						
						[
							'region_name' => '路北区',
							'region_area' => '',
						],
						
						[
							'region_name' => '路南区',
							'region_area' => '',
						],
						
						[
							'region_name' => '迁安市',
							'region_area' => '',
						],
						
						[
							'region_name' => '迁西县',
							'region_area' => '',
						],
						
						[
							'region_name' => '遵化市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '秦皇岛市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '北戴河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '卢龙县',
							'region_area' => '',
						],
						
						[
							'region_name' => '山海关区',
							'region_area' => '',
						],
						
						[
							'region_name' => '抚宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '昌黎县',
							'region_area' => '',
						],
						
						[
							'region_name' => '海港区',
							'region_area' => '',
						],
						
						[
							'region_name' => '青龙满族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '邯郸市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '丛台区',
							'region_area' => '',
						],
						
						[
							'region_name' => '临漳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '复兴区',
							'region_area' => '',
						],
						
						[
							'region_name' => '大名县',
							'region_area' => '',
						],
						
						[
							'region_name' => '峰峰矿区',
							'region_area' => '',
						],
						
						[
							'region_name' => '广平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '成安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '曲周县',
							'region_area' => '',
						],
						
						[
							'region_name' => '武安市',
							'region_area' => '',
						],
						
						[
							'region_name' => '永年县',
							'region_area' => '',
						],
						
						[
							'region_name' => '涉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '磁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '肥乡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '邯山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '邯郸县',
							'region_area' => '',
						],
						
						[
							'region_name' => '邱县',
							'region_area' => '',
						],
						
						[
							'region_name' => '馆陶县',
							'region_area' => '',
						],
						
						[
							'region_name' => '魏县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鸡泽县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '邢台市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '临城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '临西县',
							'region_area' => '',
						],
						
						[
							'region_name' => '任县',
							'region_area' => '',
						],
						
						[
							'region_name' => '内丘县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南和县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南宫市',
							'region_area' => '',
						],
						
						[
							'region_name' => '威县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宁晋县',
							'region_area' => '',
						],
						
						[
							'region_name' => '巨鹿县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平乡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '广宗县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新河县',
							'region_area' => '',
						],
						
						[
							'region_name' => '柏乡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '桥东区',
							'region_area' => '',
						],
						
						[
							'region_name' => '桥西区',
							'region_area' => '',
						],
						
						[
							'region_name' => '沙河市',
							'region_area' => '',
						],
						
						[
							'region_name' => '清河县',
							'region_area' => '',
						],
						
						[
							'region_name' => '邢台县',
							'region_area' => '',
						],
						
						[
							'region_name' => '隆尧县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '保定市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '北市区',
							'region_area' => '',
						],
						
						[
							'region_name' => '南市区',
							'region_area' => '',
						],
						
						[
							'region_name' => '博野县',
							'region_area' => '',
						],
						
						[
							'region_name' => '唐县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安国市',
							'region_area' => '',
						],
						
						[
							'region_name' => '安新县',
							'region_area' => '',
						],
						
						[
							'region_name' => '定兴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '定州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '容城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '徐水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新市区',
							'region_area' => '',
						],
						
						[
							'region_name' => '易县',
							'region_area' => '',
						],
						
						[
							'region_name' => '曲阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '望都县',
							'region_area' => '',
						],
						
						[
							'region_name' => '涞水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '涞源县',
							'region_area' => '',
						],
						
						[
							'region_name' => '涿州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '清苑县',
							'region_area' => '',
						],
						
						[
							'region_name' => '满城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '蠡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '阜平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '雄县',
							'region_area' => '',
						],
						
						[
							'region_name' => '顺平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '高碑店市',
							'region_area' => '',
						],
						
						[
							'region_name' => '高阳县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '张家口市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '万全县',
							'region_area' => '',
						],
						
						[
							'region_name' => '下花园区',
							'region_area' => '',
						],
						
						[
							'region_name' => '宣化区',
							'region_area' => '',
						],
						
						[
							'region_name' => '宣化县',
							'region_area' => '',
						],
						
						[
							'region_name' => '尚义县',
							'region_area' => '',
						],
						
						[
							'region_name' => '崇礼县',
							'region_area' => '',
						],
						
						[
							'region_name' => '康保县',
							'region_area' => '',
						],
						
						[
							'region_name' => '张北县',
							'region_area' => '',
						],
						
						[
							'region_name' => '怀安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '怀来县',
							'region_area' => '',
						],
						
						[
							'region_name' => '桥东区',
							'region_area' => '',
						],
						
						[
							'region_name' => '桥西区',
							'region_area' => '',
						],
						
						[
							'region_name' => '沽源县',
							'region_area' => '',
						],
						
						[
							'region_name' => '涿鹿县',
							'region_area' => '',
						],
						
						[
							'region_name' => '蔚县',
							'region_area' => '',
						],
						
						[
							'region_name' => '赤城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '阳原县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '承德市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '丰宁满族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '兴隆县',
							'region_area' => '',
						],
						
						[
							'region_name' => '双桥区',
							'region_area' => '',
						],
						
						[
							'region_name' => '双滦区',
							'region_area' => '',
						],
						
						[
							'region_name' => '围场满族蒙古族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宽城满族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平泉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '承德县',
							'region_area' => '',
						],
						
						[
							'region_name' => '滦平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '隆化县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鹰手营子矿区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '衡水市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '冀州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '安平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '故城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '景县',
							'region_area' => '',
						],
						
						[
							'region_name' => '枣强县',
							'region_area' => '',
						],
						
						[
							'region_name' => '桃城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '武强县',
							'region_area' => '',
						],
						
						[
							'region_name' => '武邑县',
							'region_area' => '',
						],
						
						[
							'region_name' => '深州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '阜城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '饶阳县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '廊坊市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '三河市',
							'region_area' => '',
						],
						
						[
							'region_name' => '固安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '大厂回族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '大城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安次区',
							'region_area' => '',
						],
						
						[
							'region_name' => '广阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '文安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永清县',
							'region_area' => '',
						],
						
						[
							'region_name' => '霸州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '香河县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '沧州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东光县',
							'region_area' => '',
						],
						
						[
							'region_name' => '任丘市',
							'region_area' => '',
						],
						
						[
							'region_name' => '南皮县',
							'region_area' => '',
						],
						
						[
							'region_name' => '吴桥县',
							'region_area' => '',
						],
						
						[
							'region_name' => '孟村回族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新华区',
							'region_area' => '',
						],
						
						[
							'region_name' => '沧县',
							'region_area' => '',
						],
						
						[
							'region_name' => '河间市',
							'region_area' => '',
						],
						
						[
							'region_name' => '泊头市',
							'region_area' => '',
						],
						
						[
							'region_name' => '海兴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '献县',
							'region_area' => '',
						],
						
						[
							'region_name' => '盐山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '肃宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '运河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '青县',
							'region_area' => '',
						],
						
						[
							'region_name' => '黄骅市',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '山西',
					'region_area' => '华北',
					'children' => 
					[
					
					[
						'region_name' => '太原市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '万柏林区',
							'region_area' => '',
						],
						
						[
							'region_name' => '古交市',
							'region_area' => '',
						],
						
						[
							'region_name' => '娄烦县',
							'region_area' => '',
						],
						
						[
							'region_name' => '小店区',
							'region_area' => '',
						],
						
						[
							'region_name' => '尖草坪区',
							'region_area' => '',
						],
						
						[
							'region_name' => '晋源区',
							'region_area' => '',
						],
						
						[
							'region_name' => '杏花岭区',
							'region_area' => '',
						],
						
						[
							'region_name' => '清徐县',
							'region_area' => '',
						],
						
						[
							'region_name' => '迎泽区',
							'region_area' => '',
						],
						
						[
							'region_name' => '阳曲县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '大同市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '南郊区',
							'region_area' => '',
						],
						
						[
							'region_name' => '城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '大同县',
							'region_area' => '',
						],
						
						[
							'region_name' => '天镇县',
							'region_area' => '',
						],
						
						[
							'region_name' => '左云县',
							'region_area' => '',
						],
						
						[
							'region_name' => '广灵县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新荣区',
							'region_area' => '',
						],
						
						[
							'region_name' => '浑源县',
							'region_area' => '',
						],
						
						[
							'region_name' => '灵丘县',
							'region_area' => '',
						],
						
						[
							'region_name' => '矿区',
							'region_area' => '',
						],
						
						[
							'region_name' => '阳高县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '阳泉市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '平定县',
							'region_area' => '',
						],
						
						[
							'region_name' => '盂县',
							'region_area' => '',
						],
						
						[
							'region_name' => '矿区',
							'region_area' => '',
						],
						
						[
							'region_name' => '郊区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '长治市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '壶关县',
							'region_area' => '',
						],
						
						[
							'region_name' => '屯留县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平顺县',
							'region_area' => '',
						],
						
						[
							'region_name' => '武乡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '沁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '沁源县',
							'region_area' => '',
						],
						
						[
							'region_name' => '潞城市',
							'region_area' => '',
						],
						
						[
							'region_name' => '襄垣县',
							'region_area' => '',
						],
						
						[
							'region_name' => '郊区',
							'region_area' => '',
						],
						
						[
							'region_name' => '长子县',
							'region_area' => '',
						],
						
						[
							'region_name' => '长治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '黎城县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '晋城市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '沁水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '泽州县',
							'region_area' => '',
						],
						
						[
							'region_name' => '阳城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '陵川县',
							'region_area' => '',
						],
						
						[
							'region_name' => '高平市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '朔州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '右玉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '山阴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平鲁区',
							'region_area' => '',
						],
						
						[
							'region_name' => '应县',
							'region_area' => '',
						],
						
						[
							'region_name' => '怀仁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '朔城区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '晋中市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '介休市',
							'region_area' => '',
						],
						
						[
							'region_name' => '和顺县',
							'region_area' => '',
						],
						
						[
							'region_name' => '太谷县',
							'region_area' => '',
						],
						
						[
							'region_name' => '寿阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '左权县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平遥县',
							'region_area' => '',
						],
						
						[
							'region_name' => '昔阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '榆次区',
							'region_area' => '',
						],
						
						[
							'region_name' => '榆社县',
							'region_area' => '',
						],
						
						[
							'region_name' => '灵石县',
							'region_area' => '',
						],
						
						[
							'region_name' => '祁县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '运城市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '万荣县',
							'region_area' => '',
						],
						
						[
							'region_name' => '临猗县',
							'region_area' => '',
						],
						
						[
							'region_name' => '垣曲县',
							'region_area' => '',
						],
						
						[
							'region_name' => '夏县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平陆县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新绛县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永济市',
							'region_area' => '',
						],
						
						[
							'region_name' => '河津市',
							'region_area' => '',
						],
						
						[
							'region_name' => '盐湖区',
							'region_area' => '',
						],
						
						[
							'region_name' => '稷山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '绛县',
							'region_area' => '',
						],
						
						[
							'region_name' => '芮城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '闻喜县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '忻州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '五台县',
							'region_area' => '',
						],
						
						[
							'region_name' => '五寨县',
							'region_area' => '',
						],
						
						[
							'region_name' => '代县',
							'region_area' => '',
						],
						
						[
							'region_name' => '保德县',
							'region_area' => '',
						],
						
						[
							'region_name' => '偏关县',
							'region_area' => '',
						],
						
						[
							'region_name' => '原平市',
							'region_area' => '',
						],
						
						[
							'region_name' => '宁武县',
							'region_area' => '',
						],
						
						[
							'region_name' => '定襄县',
							'region_area' => '',
						],
						
						[
							'region_name' => '岢岚县',
							'region_area' => '',
						],
						
						[
							'region_name' => '忻府区',
							'region_area' => '',
						],
						
						[
							'region_name' => '河曲县',
							'region_area' => '',
						],
						
						[
							'region_name' => '神池县',
							'region_area' => '',
						],
						
						[
							'region_name' => '繁峙县',
							'region_area' => '',
						],
						
						[
							'region_name' => '静乐县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '临汾市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '乡宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '侯马市',
							'region_area' => '',
						],
						
						[
							'region_name' => '古县',
							'region_area' => '',
						],
						
						[
							'region_name' => '吉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '大宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安泽县',
							'region_area' => '',
						],
						
						[
							'region_name' => '尧都区',
							'region_area' => '',
						],
						
						[
							'region_name' => '曲沃县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永和县',
							'region_area' => '',
						],
						
						[
							'region_name' => '汾西县',
							'region_area' => '',
						],
						
						[
							'region_name' => '洪洞县',
							'region_area' => '',
						],
						
						[
							'region_name' => '浮山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '翼城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '蒲县',
							'region_area' => '',
						],
						
						[
							'region_name' => '襄汾县',
							'region_area' => '',
						],
						
						[
							'region_name' => '隰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '霍州市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '吕梁市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '中阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '临县',
							'region_area' => '',
						],
						
						[
							'region_name' => '交口县',
							'region_area' => '',
						],
						
						[
							'region_name' => '交城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '兴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '孝义市',
							'region_area' => '',
						],
						
						[
							'region_name' => '岚县',
							'region_area' => '',
						],
						
						[
							'region_name' => '文水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '方山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '柳林县',
							'region_area' => '',
						],
						
						[
							'region_name' => '汾阳市',
							'region_area' => '',
						],
						
						[
							'region_name' => '石楼县',
							'region_area' => '',
						],
						
						[
							'region_name' => '离石区',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '内蒙古',
					'region_area' => '华北',
					'children' => 
					[
					
					[
						'region_name' => '呼和浩特市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '和林格尔县',
							'region_area' => '',
						],
						
						[
							'region_name' => '回民区',
							'region_area' => '',
						],
						
						[
							'region_name' => '土默特左旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '托克托县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '武川县',
							'region_area' => '',
						],
						
						[
							'region_name' => '清水河县',
							'region_area' => '',
						],
						
						[
							'region_name' => '玉泉区',
							'region_area' => '',
						],
						
						[
							'region_name' => '赛罕区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '包头市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '九原区',
							'region_area' => '',
						],
						
						[
							'region_name' => '固阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '土默特右旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '昆都仑区',
							'region_area' => '',
						],
						
						[
							'region_name' => '白云矿区',
							'region_area' => '',
						],
						
						[
							'region_name' => '石拐区',
							'region_area' => '',
						],
						
						[
							'region_name' => '达尔罕茂明安联合旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '青山区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '乌海市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '乌达区',
							'region_area' => '',
						],
						
						[
							'region_name' => '海勃湾区',
							'region_area' => '',
						],
						
						[
							'region_name' => '海南区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '赤峰市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '元宝山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '克什克腾旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '喀喇沁旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '宁城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '巴林右旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '巴林左旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '敖汉旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '松山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '林西县',
							'region_area' => '',
						],
						
						[
							'region_name' => '红山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '翁牛特旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '阿鲁科尔沁旗',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '通辽市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '奈曼旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '库伦旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '开鲁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '扎鲁特旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '科尔沁区',
							'region_area' => '',
						],
						
						[
							'region_name' => '科尔沁左翼中旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '科尔沁左翼后旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '霍林郭勒市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '鄂尔多斯市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东胜区',
							'region_area' => '',
						],
						
						[
							'region_name' => '乌审旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '伊金霍洛旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '准格尔旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '杭锦旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '达拉特旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '鄂东胜区',
							'region_area' => '',
						],
						
						[
							'region_name' => '鄂托克前旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '鄂托克旗',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '呼伦贝尔市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '扎兰屯市',
							'region_area' => '',
						],
						
						[
							'region_name' => '新巴尔虎右旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '新巴尔虎左旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '根河市',
							'region_area' => '',
						],
						
						[
							'region_name' => '海拉尔区',
							'region_area' => '',
						],
						
						[
							'region_name' => '满洲里市',
							'region_area' => '',
						],
						
						[
							'region_name' => '牙克石市',
							'region_area' => '',
						],
						
						[
							'region_name' => '莫力达瓦达斡尔族自治旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '鄂伦春自治旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '鄂温克族自治旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '阿荣旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '陈巴尔虎旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '额尔古纳市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '巴彦淖尔市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '临河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '乌拉特中旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '乌拉特前旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '乌拉特后旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '五原县',
							'region_area' => '',
						],
						
						[
							'region_name' => '杭锦后旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '磴口县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '乌兰察布市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '丰镇市',
							'region_area' => '',
						],
						
						[
							'region_name' => '兴和县',
							'region_area' => '',
						],
						
						[
							'region_name' => '凉城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '化德县',
							'region_area' => '',
						],
						
						[
							'region_name' => '卓资县',
							'region_area' => '',
						],
						
						[
							'region_name' => '商都县',
							'region_area' => '',
						],
						
						[
							'region_name' => '四子王旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '察哈尔右翼中旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '察哈尔右翼前旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '察哈尔右翼后旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '集宁区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '兴安盟',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '乌兰浩特市',
							'region_area' => '',
						],
						
						[
							'region_name' => '扎赉特旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '科尔沁右翼中旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '科尔沁右翼前旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '突泉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '阿尔山市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '锡林郭勒盟',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东乌珠穆沁旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '二连浩特市',
							'region_area' => '',
						],
						
						[
							'region_name' => '多伦县',
							'region_area' => '',
						],
						
						[
							'region_name' => '太仆寺旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '正蓝旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '正镶白旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '苏尼特右旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '苏尼特左旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '西乌珠穆沁旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '锡林浩特市',
							'region_area' => '',
						],
						
						[
							'region_name' => '镶黄旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '阿巴嘎旗',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '阿拉善盟',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '阿拉善右旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '阿拉善左旗',
							'region_area' => '',
						],
						
						[
							'region_name' => '额济纳旗',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '辽宁',
					'region_area' => '东北',
					'children' => 
					[
					
					[
						'region_name' => '沈阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东陵区',
							'region_area' => '',
						],
						
						[
							'region_name' => '于洪区',
							'region_area' => '',
						],
						
						[
							'region_name' => '和平区',
							'region_area' => '',
						],
						
						[
							'region_name' => '大东区',
							'region_area' => '',
						],
						
						[
							'region_name' => '康平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新民市',
							'region_area' => '',
						],
						
						[
							'region_name' => '沈北新区',
							'region_area' => '',
						],
						
						[
							'region_name' => '沈河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '法库县',
							'region_area' => '',
						],
						
						[
							'region_name' => '皇姑区',
							'region_area' => '',
						],
						
						[
							'region_name' => '苏家屯区',
							'region_area' => '',
						],
						
						[
							'region_name' => '辽中县',
							'region_area' => '',
						],
						
						[
							'region_name' => '铁西区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '大连市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '中山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '庄河市',
							'region_area' => '',
						],
						
						[
							'region_name' => '旅顺口区',
							'region_area' => '',
						],
						
						[
							'region_name' => '普兰店市',
							'region_area' => '',
						],
						
						[
							'region_name' => '沙河口区',
							'region_area' => '',
						],
						
						[
							'region_name' => '瓦房店市',
							'region_area' => '',
						],
						
						[
							'region_name' => '甘井子区',
							'region_area' => '',
						],
						
						[
							'region_name' => '西岗区',
							'region_area' => '',
						],
						
						[
							'region_name' => '金州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '长海县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '鞍山市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '千山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '台安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '岫岩满族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '海城市',
							'region_area' => '',
						],
						
						[
							'region_name' => '立山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '铁东区',
							'region_area' => '',
						],
						
						[
							'region_name' => '铁西区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '抚顺市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东洲区',
							'region_area' => '',
						],
						
						[
							'region_name' => '抚顺县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新宾满族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新抚区',
							'region_area' => '',
						],
						
						[
							'region_name' => '望花区',
							'region_area' => '',
						],
						
						[
							'region_name' => '清原满族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '顺城区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '本溪市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '南芬区',
							'region_area' => '',
						],
						
						[
							'region_name' => '平山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '明山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '本溪满族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '桓仁满族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '溪湖区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '丹东市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东港市',
							'region_area' => '',
						],
						
						[
							'region_name' => '元宝区',
							'region_area' => '',
						],
						
						[
							'region_name' => '凤城市',
							'region_area' => '',
						],
						
						[
							'region_name' => '宽甸满族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '振兴区',
							'region_area' => '',
						],
						
						[
							'region_name' => '振安区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '锦州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '义县',
							'region_area' => '',
						],
						
						[
							'region_name' => '凌河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '凌海市',
							'region_area' => '',
						],
						
						[
							'region_name' => '北镇市',
							'region_area' => '',
						],
						
						[
							'region_name' => '古塔区',
							'region_area' => '',
						],
						
						[
							'region_name' => '太和区',
							'region_area' => '',
						],
						
						[
							'region_name' => '黑山县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '营口市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '大石桥市',
							'region_area' => '',
						],
						
						[
							'region_name' => '盖州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '站前区',
							'region_area' => '',
						],
						
						[
							'region_name' => '老边区',
							'region_area' => '',
						],
						
						[
							'region_name' => '西市区',
							'region_area' => '',
						],
						
						[
							'region_name' => '鲅鱼圈区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '阜新市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '太平区',
							'region_area' => '',
						],
						
						[
							'region_name' => '彰武县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新邱区',
							'region_area' => '',
						],
						
						[
							'region_name' => '海州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '清河门区',
							'region_area' => '',
						],
						
						[
							'region_name' => '细河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '蒙古族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '辽阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '太子河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '宏伟区',
							'region_area' => '',
						],
						
						[
							'region_name' => '弓长岭区',
							'region_area' => '',
						],
						
						[
							'region_name' => '文圣区',
							'region_area' => '',
						],
						
						[
							'region_name' => '灯塔市',
							'region_area' => '',
						],
						
						[
							'region_name' => '白塔区',
							'region_area' => '',
						],
						
						[
							'region_name' => '辽阳县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '盘锦市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '兴隆台区',
							'region_area' => '',
						],
						
						[
							'region_name' => '双台子区',
							'region_area' => '',
						],
						
						[
							'region_name' => '大洼县',
							'region_area' => '',
						],
						
						[
							'region_name' => '盘山县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '铁岭市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '开原市',
							'region_area' => '',
						],
						
						[
							'region_name' => '昌图县',
							'region_area' => '',
						],
						
						[
							'region_name' => '清河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '西丰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '调兵山市',
							'region_area' => '',
						],
						
						[
							'region_name' => '铁岭县',
							'region_area' => '',
						],
						
						[
							'region_name' => '银州区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '朝阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '凌源市',
							'region_area' => '',
						],
						
						[
							'region_name' => '北票市',
							'region_area' => '',
						],
						
						[
							'region_name' => '双塔区',
							'region_area' => '',
						],
						
						[
							'region_name' => '喀喇沁左翼蒙古族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '建平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '朝阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙城区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '葫芦岛市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '兴城市',
							'region_area' => '',
						],
						
						[
							'region_name' => '南票区',
							'region_area' => '',
						],
						
						[
							'region_name' => '建昌县',
							'region_area' => '',
						],
						
						[
							'region_name' => '绥中县',
							'region_area' => '',
						],
						
						[
							'region_name' => '连山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙港区',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '吉林',
					'region_area' => '东北',
					'children' => 
					[
					
					[
						'region_name' => '长春市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '九台市',
							'region_area' => '',
						],
						
						[
							'region_name' => '二道区',
							'region_area' => '',
						],
						
						[
							'region_name' => '农安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南关区',
							'region_area' => '',
						],
						
						[
							'region_name' => '双阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '宽城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '德惠市',
							'region_area' => '',
						],
						
						[
							'region_name' => '朝阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '榆树市',
							'region_area' => '',
						],
						
						[
							'region_name' => '绿园区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '吉林市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '丰满区',
							'region_area' => '',
						],
						
						[
							'region_name' => '昌邑区',
							'region_area' => '',
						],
						
						[
							'region_name' => '桦甸市',
							'region_area' => '',
						],
						
						[
							'region_name' => '永吉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '磐石市',
							'region_area' => '',
						],
						
						[
							'region_name' => '舒兰市',
							'region_area' => '',
						],
						
						[
							'region_name' => '船营区',
							'region_area' => '',
						],
						
						[
							'region_name' => '蛟河市',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙潭区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '四平市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '伊通满族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '公主岭市',
							'region_area' => '',
						],
						
						[
							'region_name' => '双辽市',
							'region_area' => '',
						],
						
						[
							'region_name' => '梨树县',
							'region_area' => '',
						],
						
						[
							'region_name' => '铁东区',
							'region_area' => '',
						],
						
						[
							'region_name' => '铁西区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '辽源市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东丰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '东辽县',
							'region_area' => '',
						],
						
						[
							'region_name' => '西安区',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙山区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '通化市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东昌区',
							'region_area' => '',
						],
						
						[
							'region_name' => '二道江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '柳河县',
							'region_area' => '',
						],
						
						[
							'region_name' => '梅河口市',
							'region_area' => '',
						],
						
						[
							'region_name' => '辉南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '通化县',
							'region_area' => '',
						],
						
						[
							'region_name' => '集安市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '白山市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '临江市',
							'region_area' => '',
						],
						
						[
							'region_name' => '八道江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '抚松县',
							'region_area' => '',
						],
						
						[
							'region_name' => '江源区',
							'region_area' => '',
						],
						
						[
							'region_name' => '长白朝鲜族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '靖宇县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '松原市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '干安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '前郭尔罗斯蒙古族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宁江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '扶余县',
							'region_area' => '',
						],
						
						[
							'region_name' => '长岭县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '白城市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '大安市',
							'region_area' => '',
						],
						
						[
							'region_name' => '洮北区',
							'region_area' => '',
						],
						
						[
							'region_name' => '洮南市',
							'region_area' => '',
						],
						
						[
							'region_name' => '通榆县',
							'region_area' => '',
						],
						
						[
							'region_name' => '镇赉县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '延边朝鲜族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '和龙市',
							'region_area' => '',
						],
						
						[
							'region_name' => '图们市',
							'region_area' => '',
						],
						
						[
							'region_name' => '安图县',
							'region_area' => '',
						],
						
						[
							'region_name' => '延吉市',
							'region_area' => '',
						],
						
						[
							'region_name' => '敦化市',
							'region_area' => '',
						],
						
						[
							'region_name' => '汪清县',
							'region_area' => '',
						],
						
						[
							'region_name' => '珲春市',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙井市',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '黑龙江',
					'region_area' => '东北',
					'children' => 
					[
					
					[
						'region_name' => '哈尔滨市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '五常市',
							'region_area' => '',
						],
						
						[
							'region_name' => '依兰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南岗区',
							'region_area' => '',
						],
						
						[
							'region_name' => '双城市',
							'region_area' => '',
						],
						
						[
							'region_name' => '呼兰区',
							'region_area' => '',
						],
						
						[
							'region_name' => '哈尔滨市道里区',
							'region_area' => '',
						],
						
						[
							'region_name' => '宾县',
							'region_area' => '',
						],
						
						[
							'region_name' => '尚志市',
							'region_area' => '',
						],
						
						[
							'region_name' => '巴彦县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平房区',
							'region_area' => '',
						],
						
						[
							'region_name' => '延寿县',
							'region_area' => '',
						],
						
						[
							'region_name' => '方正县',
							'region_area' => '',
						],
						
						[
							'region_name' => '木兰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '松北区',
							'region_area' => '',
						],
						
						[
							'region_name' => '通河县',
							'region_area' => '',
						],
						
						[
							'region_name' => '道外区',
							'region_area' => '',
						],
						
						[
							'region_name' => '阿城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '香坊区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '齐齐哈尔市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '依安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '克东县',
							'region_area' => '',
						],
						
						[
							'region_name' => '克山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '富拉尔基区',
							'region_area' => '',
						],
						
						[
							'region_name' => '富裕县',
							'region_area' => '',
						],
						
						[
							'region_name' => '建华区',
							'region_area' => '',
						],
						
						[
							'region_name' => '拜泉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '昂昂溪区',
							'region_area' => '',
						],
						
						[
							'region_name' => '梅里斯达斡尔族区',
							'region_area' => '',
						],
						
						[
							'region_name' => '泰来县',
							'region_area' => '',
						],
						
						[
							'region_name' => '甘南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '碾子山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '讷河市',
							'region_area' => '',
						],
						
						[
							'region_name' => '铁锋区',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙沙区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '鸡西市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '城子河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '密山市',
							'region_area' => '',
						],
						
						[
							'region_name' => '恒山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '梨树区',
							'region_area' => '',
						],
						
						[
							'region_name' => '滴道区',
							'region_area' => '',
						],
						
						[
							'region_name' => '虎林市',
							'region_area' => '',
						],
						
						[
							'region_name' => '鸡东县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鸡冠区',
							'region_area' => '',
						],
						
						[
							'region_name' => '麻山区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '鹤岗市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '兴安区',
							'region_area' => '',
						],
						
						[
							'region_name' => '兴山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '南山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '向阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '工农区',
							'region_area' => '',
						],
						
						[
							'region_name' => '绥滨县',
							'region_area' => '',
						],
						
						[
							'region_name' => '萝北县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '双鸭山市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '友谊县',
							'region_area' => '',
						],
						
						[
							'region_name' => '四方台区',
							'region_area' => '',
						],
						
						[
							'region_name' => '宝山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '宝清县',
							'region_area' => '',
						],
						
						[
							'region_name' => '尖山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '岭东区',
							'region_area' => '',
						],
						
						[
							'region_name' => '集贤县',
							'region_area' => '',
						],
						
						[
							'region_name' => '饶河县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '大庆市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '大同区',
							'region_area' => '',
						],
						
						[
							'region_name' => '杜尔伯特蒙古族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '林甸县',
							'region_area' => '',
						],
						
						[
							'region_name' => '红岗区',
							'region_area' => '',
						],
						
						[
							'region_name' => '肇州县',
							'region_area' => '',
						],
						
						[
							'region_name' => '肇源县',
							'region_area' => '',
						],
						
						[
							'region_name' => '胡路区',
							'region_area' => '',
						],
						
						[
							'region_name' => '萨尔图区',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙凤区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '伊春市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '上甘岭区',
							'region_area' => '',
						],
						
						[
							'region_name' => '乌伊岭区',
							'region_area' => '',
						],
						
						[
							'region_name' => '乌马河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '五营区',
							'region_area' => '',
						],
						
						[
							'region_name' => '伊春区',
							'region_area' => '',
						],
						
						[
							'region_name' => '南岔区',
							'region_area' => '',
						],
						
						[
							'region_name' => '友好区',
							'region_area' => '',
						],
						
						[
							'region_name' => '嘉荫县',
							'region_area' => '',
						],
						
						[
							'region_name' => '带岭区',
							'region_area' => '',
						],
						
						[
							'region_name' => '新青区',
							'region_area' => '',
						],
						
						[
							'region_name' => '汤旺河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '红星区',
							'region_area' => '',
						],
						
						[
							'region_name' => '美溪区',
							'region_area' => '',
						],
						
						[
							'region_name' => '翠峦区',
							'region_area' => '',
						],
						
						[
							'region_name' => '西林区',
							'region_area' => '',
						],
						
						[
							'region_name' => '金山屯区',
							'region_area' => '',
						],
						
						[
							'region_name' => '铁力市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '佳木斯市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东风区',
							'region_area' => '',
						],
						
						[
							'region_name' => '前进区',
							'region_area' => '',
						],
						
						[
							'region_name' => '同江市',
							'region_area' => '',
						],
						
						[
							'region_name' => '向阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '富锦市',
							'region_area' => '',
						],
						
						[
							'region_name' => '抚远县',
							'region_area' => '',
						],
						
						[
							'region_name' => '桦南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '桦川县',
							'region_area' => '',
						],
						
						[
							'region_name' => '汤原县',
							'region_area' => '',
						],
						
						[
							'region_name' => '郊区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '七台河市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '勃利县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新兴区',
							'region_area' => '',
						],
						
						[
							'region_name' => '桃山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '茄子河区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '牡丹江市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '东安区',
							'region_area' => '',
						],
						
						[
							'region_name' => '宁安市',
							'region_area' => '',
						],
						
						[
							'region_name' => '林口县',
							'region_area' => '',
						],
						
						[
							'region_name' => '海林市',
							'region_area' => '',
						],
						
						[
							'region_name' => '爱民区',
							'region_area' => '',
						],
						
						[
							'region_name' => '穆棱市',
							'region_area' => '',
						],
						
						[
							'region_name' => '绥芬河市',
							'region_area' => '',
						],
						
						[
							'region_name' => '西安区',
							'region_area' => '',
						],
						
						[
							'region_name' => '阳明区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '黑河市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '五大连池市',
							'region_area' => '',
						],
						
						[
							'region_name' => '北安市',
							'region_area' => '',
						],
						
						[
							'region_name' => '嫩江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '孙吴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '爱辉区',
							'region_area' => '',
						],
						
						[
							'region_name' => '车逊克县',
							'region_area' => '',
						],
						
						[
							'region_name' => '逊克县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '绥化市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '兰西县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安达市',
							'region_area' => '',
						],
						
						[
							'region_name' => '庆安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '明水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '望奎县',
							'region_area' => '',
						],
						
						[
							'region_name' => '海伦市',
							'region_area' => '',
						],
						
						[
							'region_name' => '绥化市北林区',
							'region_area' => '',
						],
						
						[
							'region_name' => '绥棱县',
							'region_area' => '',
						],
						
						[
							'region_name' => '肇东市',
							'region_area' => '',
						],
						
						[
							'region_name' => '青冈县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '大兴安岭地区',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '呼玛县',
							'region_area' => '',
						],
						
						[
							'region_name' => '塔河县',
							'region_area' => '',
						],
						
						[
							'region_name' => '大兴安岭地区加格达奇区',
							'region_area' => '',
						],
						
						[
							'region_name' => '大兴安岭地区呼中区',
							'region_area' => '',
						],
						
						[
							'region_name' => '大兴安岭地区新林区',
							'region_area' => '',
						],
						
						[
							'region_name' => '大兴安岭地区松岭区',
							'region_area' => '',
						],
						
						[
							'region_name' => '漠河县',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '上海',
					'region_area' => '华东',
					'children' => 
					[
					
					[
						'region_name' => '上海市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '黄浦区',
							'region_area' => '',
						],
						
						[
							'region_name' => '卢湾区',
							'region_area' => '',
						],
						
						[
							'region_name' => '徐汇区',
							'region_area' => '',
						],
						
						[
							'region_name' => '长宁区',
							'region_area' => '',
						],
						
						[
							'region_name' => '静安区',
							'region_area' => '',
						],
						
						[
							'region_name' => '普陀区',
							'region_area' => '',
						],
						
						[
							'region_name' => '闸北区',
							'region_area' => '',
						],
						
						[
							'region_name' => '虹口区',
							'region_area' => '',
						],
						
						[
							'region_name' => '杨浦区',
							'region_area' => '',
						],
						
						[
							'region_name' => '闵行区',
							'region_area' => '',
						],
						
						[
							'region_name' => '宝山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '嘉定区',
							'region_area' => '',
						],
						
						[
							'region_name' => '浦东新区',
							'region_area' => '',
						],
						
						[
							'region_name' => '金山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '松江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '青浦区',
							'region_area' => '',
						],
						
						[
							'region_name' => '南汇区',
							'region_area' => '',
						],
						
						[
							'region_name' => '奉贤区',
							'region_area' => '',
						],
						
						[
							'region_name' => '崇明县',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '江苏',
					'region_area' => '华东',
					'children' => 
					[
					
					[
						'region_name' => '南京市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '下关区',
							'region_area' => '',
						],
						
						[
							'region_name' => '六合区',
							'region_area' => '',
						],
						
						[
							'region_name' => '建邺区',
							'region_area' => '',
						],
						
						[
							'region_name' => '栖霞区',
							'region_area' => '',
						],
						
						[
							'region_name' => '江宁区',
							'region_area' => '',
						],
						
						[
							'region_name' => '浦口区',
							'region_area' => '',
						],
						
						[
							'region_name' => '溧水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '玄武区',
							'region_area' => '',
						],
						
						[
							'region_name' => '白下区',
							'region_area' => '',
						],
						
						[
							'region_name' => '秦淮区',
							'region_area' => '',
						],
						
						[
							'region_name' => '雨花台区',
							'region_area' => '',
						],
						
						[
							'region_name' => '高淳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鼓楼区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '无锡市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '北塘区',
							'region_area' => '',
						],
						
						[
							'region_name' => '南长区',
							'region_area' => '',
						],
						
						[
							'region_name' => '宜兴市',
							'region_area' => '',
						],
						
						[
							'region_name' => '崇安区',
							'region_area' => '',
						],
						
						[
							'region_name' => '惠山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '江阴市',
							'region_area' => '',
						],
						
						[
							'region_name' => '滨湖区',
							'region_area' => '',
						],
						
						[
							'region_name' => '锡山区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '徐州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '丰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '九里区',
							'region_area' => '',
						],
						
						[
							'region_name' => '云龙区',
							'region_area' => '',
						],
						
						[
							'region_name' => '新沂市',
							'region_area' => '',
						],
						
						[
							'region_name' => '沛县',
							'region_area' => '',
						],
						
						[
							'region_name' => '泉山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '睢宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '贾汪区',
							'region_area' => '',
						],
						
						[
							'region_name' => '邳州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '铜山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鼓楼区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '常州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '天宁区',
							'region_area' => '',
						],
						
						[
							'region_name' => '戚墅堰区',
							'region_area' => '',
						],
						
						[
							'region_name' => '新北区',
							'region_area' => '',
						],
						
						[
							'region_name' => '武进区',
							'region_area' => '',
						],
						
						[
							'region_name' => '溧阳市',
							'region_area' => '',
						],
						
						[
							'region_name' => '金坛市',
							'region_area' => '',
						],
						
						[
							'region_name' => '钟楼区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '苏州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '吴中区',
							'region_area' => '',
						],
						
						[
							'region_name' => '吴江市',
							'region_area' => '',
						],
						
						[
							'region_name' => '太仓市',
							'region_area' => '',
						],
						
						[
							'region_name' => '常熟市',
							'region_area' => '',
						],
						
						[
							'region_name' => '平江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '张家港市',
							'region_area' => '',
						],
						
						[
							'region_name' => '昆山市',
							'region_area' => '',
						],
						
						[
							'region_name' => '沧浪区',
							'region_area' => '',
						],
						
						[
							'region_name' => '相城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '苏州工业园区',
							'region_area' => '',
						],
						
						[
							'region_name' => '虎丘区',
							'region_area' => '',
						],
						
						[
							'region_name' => '金阊区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '南通市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '启东市',
							'region_area' => '',
						],
						
						[
							'region_name' => '如东县',
							'region_area' => '',
						],
						
						[
							'region_name' => '如皋市',
							'region_area' => '',
						],
						
						[
							'region_name' => '崇川区',
							'region_area' => '',
						],
						
						[
							'region_name' => '海安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '海门市',
							'region_area' => '',
						],
						
						[
							'region_name' => '港闸区',
							'region_area' => '',
						],
						
						[
							'region_name' => '通州市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '连云港市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东海县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新浦区',
							'region_area' => '',
						],
						
						[
							'region_name' => '海州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '灌云县',
							'region_area' => '',
						],
						
						[
							'region_name' => '灌南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '赣榆县',
							'region_area' => '',
						],
						
						[
							'region_name' => '连云区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '淮安市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '楚州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '洪泽县',
							'region_area' => '',
						],
						
						[
							'region_name' => '涟水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '淮阴区',
							'region_area' => '',
						],
						
						[
							'region_name' => '清河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '清浦区',
							'region_area' => '',
						],
						
						[
							'region_name' => '盱眙县',
							'region_area' => '',
						],
						
						[
							'region_name' => '金湖县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '盐城市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东台市',
							'region_area' => '',
						],
						
						[
							'region_name' => '亭湖区',
							'region_area' => '',
						],
						
						[
							'region_name' => '响水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '大丰市',
							'region_area' => '',
						],
						
						[
							'region_name' => '射阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '建湖县',
							'region_area' => '',
						],
						
						[
							'region_name' => '滨海县',
							'region_area' => '',
						],
						
						[
							'region_name' => '盐都区',
							'region_area' => '',
						],
						
						[
							'region_name' => '阜宁县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '扬州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '仪征市',
							'region_area' => '',
						],
						
						[
							'region_name' => '宝应县',
							'region_area' => '',
						],
						
						[
							'region_name' => '广陵区',
							'region_area' => '',
						],
						
						[
							'region_name' => '江都市',
							'region_area' => '',
						],
						
						[
							'region_name' => '维扬区',
							'region_area' => '',
						],
						
						[
							'region_name' => '邗江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '高邮市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '镇江市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '丹徒区',
							'region_area' => '',
						],
						
						[
							'region_name' => '丹阳市',
							'region_area' => '',
						],
						
						[
							'region_name' => '京口区',
							'region_area' => '',
						],
						
						[
							'region_name' => '句容市',
							'region_area' => '',
						],
						
						[
							'region_name' => '扬中市',
							'region_area' => '',
						],
						
						[
							'region_name' => '润州区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '泰州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '兴化市',
							'region_area' => '',
						],
						
						[
							'region_name' => '姜堰市',
							'region_area' => '',
						],
						
						[
							'region_name' => '泰兴市',
							'region_area' => '',
						],
						
						[
							'region_name' => '海陵区',
							'region_area' => '',
						],
						
						[
							'region_name' => '靖江市',
							'region_area' => '',
						],
						
						[
							'region_name' => '高港区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '宿迁市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '宿城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '宿豫区',
							'region_area' => '',
						],
						
						[
							'region_name' => '沭阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '泗洪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '泗阳县',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '浙江',
					'region_area' => '华东',
					'children' => 
					[
					
					[
						'region_name' => '杭州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '上城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '下城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '临安市',
							'region_area' => '',
						],
						
						[
							'region_name' => '余杭区',
							'region_area' => '',
						],
						
						[
							'region_name' => '富阳市',
							'region_area' => '',
						],
						
						[
							'region_name' => '建德市',
							'region_area' => '',
						],
						
						[
							'region_name' => '拱墅区',
							'region_area' => '',
						],
						
						[
							'region_name' => '桐庐县',
							'region_area' => '',
						],
						
						[
							'region_name' => '江干区',
							'region_area' => '',
						],
						
						[
							'region_name' => '淳安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '滨江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '萧山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '西湖区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '宁波市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '余姚市',
							'region_area' => '',
						],
						
						[
							'region_name' => '北仑区',
							'region_area' => '',
						],
						
						[
							'region_name' => '奉化市',
							'region_area' => '',
						],
						
						[
							'region_name' => '宁海县',
							'region_area' => '',
						],
						
						[
							'region_name' => '慈溪市',
							'region_area' => '',
						],
						
						[
							'region_name' => '江东区',
							'region_area' => '',
						],
						
						[
							'region_name' => '江北区',
							'region_area' => '',
						],
						
						[
							'region_name' => '海曙区',
							'region_area' => '',
						],
						
						[
							'region_name' => '象山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鄞州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '镇海区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '温州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '乐清市',
							'region_area' => '',
						],
						
						[
							'region_name' => '平阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '文成县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永嘉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '泰顺县',
							'region_area' => '',
						],
						
						[
							'region_name' => '洞头县',
							'region_area' => '',
						],
						
						[
							'region_name' => '瑞安市',
							'region_area' => '',
						],
						
						[
							'region_name' => '瓯海区',
							'region_area' => '',
						],
						
						[
							'region_name' => '苍南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鹿城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙湾区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '嘉兴市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '南湖区',
							'region_area' => '',
						],
						
						[
							'region_name' => '嘉善县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平湖市',
							'region_area' => '',
						],
						
						[
							'region_name' => '桐乡市',
							'region_area' => '',
						],
						
						[
							'region_name' => '海宁市',
							'region_area' => '',
						],
						
						[
							'region_name' => '海盐县',
							'region_area' => '',
						],
						
						[
							'region_name' => '秀洲区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '湖州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '南浔区',
							'region_area' => '',
						],
						
						[
							'region_name' => '吴兴区',
							'region_area' => '',
						],
						
						[
							'region_name' => '安吉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '德清县',
							'region_area' => '',
						],
						
						[
							'region_name' => '长兴县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '绍兴市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '上虞市',
							'region_area' => '',
						],
						
						[
							'region_name' => '嵊州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '新昌县',
							'region_area' => '',
						],
						
						[
							'region_name' => '绍兴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '诸暨市',
							'region_area' => '',
						],
						
						[
							'region_name' => '越城区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '舟山市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '定海区',
							'region_area' => '',
						],
						
						[
							'region_name' => '岱山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '嵊泗县',
							'region_area' => '',
						],
						
						[
							'region_name' => '普陀区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '衢州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '常山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '开化县',
							'region_area' => '',
						],
						
						[
							'region_name' => '柯城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '江山市',
							'region_area' => '',
						],
						
						[
							'region_name' => '衢江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙游县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '金华市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东阳市',
							'region_area' => '',
						],
						
						[
							'region_name' => '义乌市',
							'region_area' => '',
						],
						
						[
							'region_name' => '兰溪市',
							'region_area' => '',
						],
						
						[
							'region_name' => '婺城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '武义县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永康市',
							'region_area' => '',
						],
						
						[
							'region_name' => '浦江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '磐安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '金东区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '台州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '三门县',
							'region_area' => '',
						],
						
						[
							'region_name' => '临海市',
							'region_area' => '',
						],
						
						[
							'region_name' => '仙居县',
							'region_area' => '',
						],
						
						[
							'region_name' => '天台县',
							'region_area' => '',
						],
						
						[
							'region_name' => '椒江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '温岭市',
							'region_area' => '',
						],
						
						[
							'region_name' => '玉环县',
							'region_area' => '',
						],
						
						[
							'region_name' => '路桥区',
							'region_area' => '',
						],
						
						[
							'region_name' => '黄岩区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '丽水市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '云和县',
							'region_area' => '',
						],
						
						[
							'region_name' => '庆元县',
							'region_area' => '',
						],
						
						[
							'region_name' => '景宁畲族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '松阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '缙云县',
							'region_area' => '',
						],
						
						[
							'region_name' => '莲都区',
							'region_area' => '',
						],
						
						[
							'region_name' => '遂昌县',
							'region_area' => '',
						],
						
						[
							'region_name' => '青田县',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙泉市',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '安徽',
					'region_area' => '华东',
					'children' => 
					[
					
					[
						'region_name' => '合肥市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '包河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '庐阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '瑶海区',
							'region_area' => '',
						],
						
						[
							'region_name' => '肥东县',
							'region_area' => '',
						],
						
						[
							'region_name' => '肥西县',
							'region_area' => '',
						],
						
						[
							'region_name' => '蜀山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '长丰县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '芜湖市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '三山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '南陵县',
							'region_area' => '',
						],
						
						[
							'region_name' => '弋江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '繁昌县',
							'region_area' => '',
						],
						
						[
							'region_name' => '芜湖县',
							'region_area' => '',
						],
						
						[
							'region_name' => '镜湖区',
							'region_area' => '',
						],
						
						[
							'region_name' => '鸠江区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '蚌埠市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '五河县',
							'region_area' => '',
						],
						
						[
							'region_name' => '固镇县',
							'region_area' => '',
						],
						
						[
							'region_name' => '怀远县',
							'region_area' => '',
						],
						
						[
							'region_name' => '淮上区',
							'region_area' => '',
						],
						
						[
							'region_name' => '禹会区',
							'region_area' => '',
						],
						
						[
							'region_name' => '蚌山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙子湖区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '淮南市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '八公山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '凤台县',
							'region_area' => '',
						],
						
						[
							'region_name' => '大通区',
							'region_area' => '',
						],
						
						[
							'region_name' => '潘集区',
							'region_area' => '',
						],
						
						[
							'region_name' => '田家庵区',
							'region_area' => '',
						],
						
						[
							'region_name' => '谢家集区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '马鞍山市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '当涂县',
							'region_area' => '',
						],
						
						[
							'region_name' => '花山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '金家庄区',
							'region_area' => '',
						],
						
						[
							'region_name' => '雨山区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '淮北市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '杜集区',
							'region_area' => '',
						],
						
						[
							'region_name' => '濉溪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '烈山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '相山区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '铜陵市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '狮子山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '郊区',
							'region_area' => '',
						],
						
						[
							'region_name' => '铜官山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '铜陵县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '安庆市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '大观区',
							'region_area' => '',
						],
						
						[
							'region_name' => '太湖县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宜秀区',
							'region_area' => '',
						],
						
						[
							'region_name' => '宿松县',
							'region_area' => '',
						],
						
						[
							'region_name' => '岳西县',
							'region_area' => '',
						],
						
						[
							'region_name' => '怀宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '望江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '枞阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '桐城市',
							'region_area' => '',
						],
						
						[
							'region_name' => '潜山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '迎江区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '黄山市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '休宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '屯溪区',
							'region_area' => '',
						],
						
						[
							'region_name' => '徽州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '歙县',
							'region_area' => '',
						],
						
						[
							'region_name' => '祁门县',
							'region_area' => '',
						],
						
						[
							'region_name' => '黄山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '黟县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '滁州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '全椒县',
							'region_area' => '',
						],
						
						[
							'region_name' => '凤阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南谯区',
							'region_area' => '',
						],
						
						[
							'region_name' => '天长市',
							'region_area' => '',
						],
						
						[
							'region_name' => '定远县',
							'region_area' => '',
						],
						
						[
							'region_name' => '明光市',
							'region_area' => '',
						],
						
						[
							'region_name' => '来安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '琅玡区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '阜阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '临泉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '太和县',
							'region_area' => '',
						],
						
						[
							'region_name' => '界首市',
							'region_area' => '',
						],
						
						[
							'region_name' => '阜南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '颍东区',
							'region_area' => '',
						],
						
						[
							'region_name' => '颍州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '颍泉区',
							'region_area' => '',
						],
						
						[
							'region_name' => '颖上县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '宿州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '埇桥区',
							'region_area' => '',
						],
						
						[
							'region_name' => '泗县辖',
							'region_area' => '',
						],
						
						[
							'region_name' => '灵璧县',
							'region_area' => '',
						],
						
						[
							'region_name' => '砀山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '萧县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '巢湖市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '含山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '和县',
							'region_area' => '',
						],
						
						[
							'region_name' => '居巢区',
							'region_area' => '',
						],
						
						[
							'region_name' => '庐江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '无为县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '六安市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '寿县',
							'region_area' => '',
						],
						
						[
							'region_name' => '舒城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '裕安区',
							'region_area' => '',
						],
						
						[
							'region_name' => '金安区',
							'region_area' => '',
						],
						
						[
							'region_name' => '金寨县',
							'region_area' => '',
						],
						
						[
							'region_name' => '霍山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '霍邱县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '亳州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '利辛县',
							'region_area' => '',
						],
						
						[
							'region_name' => '涡阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '蒙城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '谯城区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '池州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东至县',
							'region_area' => '',
						],
						
						[
							'region_name' => '石台县',
							'region_area' => '',
						],
						
						[
							'region_name' => '贵池区',
							'region_area' => '',
						],
						
						[
							'region_name' => '青阳县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '宣城市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '宁国市',
							'region_area' => '',
						],
						
						[
							'region_name' => '宣州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '广德县',
							'region_area' => '',
						],
						
						[
							'region_name' => '旌德县',
							'region_area' => '',
						],
						
						[
							'region_name' => '泾县',
							'region_area' => '',
						],
						
						[
							'region_name' => '绩溪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '郎溪县',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '福建',
					'region_area' => '华南',
					'children' => 
					[
					
					[
						'region_name' => '福州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '仓山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '台江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '平潭县',
							'region_area' => '',
						],
						
						[
							'region_name' => '晋安区',
							'region_area' => '',
						],
						
						[
							'region_name' => '永泰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '福清市',
							'region_area' => '',
						],
						
						[
							'region_name' => '罗源县',
							'region_area' => '',
						],
						
						[
							'region_name' => '连江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '长乐市',
							'region_area' => '',
						],
						
						[
							'region_name' => '闽侯县',
							'region_area' => '',
						],
						
						[
							'region_name' => '闽清县',
							'region_area' => '',
						],
						
						[
							'region_name' => '马尾区',
							'region_area' => '',
						],
						
						[
							'region_name' => '鼓楼区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '厦门市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '同安区',
							'region_area' => '',
						],
						
						[
							'region_name' => '思明区',
							'region_area' => '',
						],
						
						[
							'region_name' => '海沧区',
							'region_area' => '',
						],
						
						[
							'region_name' => '湖里区',
							'region_area' => '',
						],
						
						[
							'region_name' => '翔安区',
							'region_area' => '',
						],
						
						[
							'region_name' => '集美区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '莆田市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '仙游县',
							'region_area' => '',
						],
						
						[
							'region_name' => '城厢区',
							'region_area' => '',
						],
						
						[
							'region_name' => '涵江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '秀屿区',
							'region_area' => '',
						],
						
						[
							'region_name' => '荔城区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '三明市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '三元区',
							'region_area' => '',
						],
						
						[
							'region_name' => '大田县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宁化县',
							'region_area' => '',
						],
						
						[
							'region_name' => '将乐县',
							'region_area' => '',
						],
						
						[
							'region_name' => '尤溪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '建宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '明溪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '梅列区',
							'region_area' => '',
						],
						
						[
							'region_name' => '永安市',
							'region_area' => '',
						],
						
						[
							'region_name' => '沙县',
							'region_area' => '',
						],
						
						[
							'region_name' => '泰宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '清流县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '泉州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '丰泽区',
							'region_area' => '',
						],
						
						[
							'region_name' => '南安市',
							'region_area' => '',
						],
						
						[
							'region_name' => '安溪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '德化县',
							'region_area' => '',
						],
						
						[
							'region_name' => '惠安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '晋江市',
							'region_area' => '',
						],
						
						[
							'region_name' => '永春县',
							'region_area' => '',
						],
						
						[
							'region_name' => '泉港区',
							'region_area' => '',
						],
						
						[
							'region_name' => '洛江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '石狮市',
							'region_area' => '',
						],
						
						[
							'region_name' => '金门县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鲤城区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '漳州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '云霄县',
							'region_area' => '',
						],
						
						[
							'region_name' => '华安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南靖县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平和县',
							'region_area' => '',
						],
						
						[
							'region_name' => '漳浦县',
							'region_area' => '',
						],
						
						[
							'region_name' => '芗城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '诏安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '长泰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙文区',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙海市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '南平市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '光泽县',
							'region_area' => '',
						],
						
						[
							'region_name' => '延平区',
							'region_area' => '',
						],
						
						[
							'region_name' => '建瓯市',
							'region_area' => '',
						],
						
						[
							'region_name' => '建阳市',
							'region_area' => '',
						],
						
						[
							'region_name' => '政和县',
							'region_area' => '',
						],
						
						[
							'region_name' => '松溪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '武夷山市',
							'region_area' => '',
						],
						
						[
							'region_name' => '浦城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '邵武市',
							'region_area' => '',
						],
						
						[
							'region_name' => '顺昌县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '龙岩市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '上杭县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新罗区',
							'region_area' => '',
						],
						
						[
							'region_name' => '武平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永定县',
							'region_area' => '',
						],
						
						[
							'region_name' => '漳平市',
							'region_area' => '',
						],
						
						[
							'region_name' => '连城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '长汀县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '宁德市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '古田县',
							'region_area' => '',
						],
						
						[
							'region_name' => '周宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '寿宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '屏南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '柘荣县',
							'region_area' => '',
						],
						
						[
							'region_name' => '福安市',
							'region_area' => '',
						],
						
						[
							'region_name' => '福鼎市',
							'region_area' => '',
						],
						
						[
							'region_name' => '蕉城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '霞浦县',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '江西',
					'region_area' => '华东',
					'children' => 
					[
					
					[
						'region_name' => '南昌市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东湖区',
							'region_area' => '',
						],
						
						[
							'region_name' => '南昌县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安义县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新建县',
							'region_area' => '',
						],
						
						[
							'region_name' => '湾里区',
							'region_area' => '',
						],
						
						[
							'region_name' => '西湖区',
							'region_area' => '',
						],
						
						[
							'region_name' => '进贤县',
							'region_area' => '',
						],
						
						[
							'region_name' => '青云谱区',
							'region_area' => '',
						],
						
						[
							'region_name' => '青山湖区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '景德镇市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '乐平市',
							'region_area' => '',
						],
						
						[
							'region_name' => '昌江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '浮梁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '珠山区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '萍乡市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '上栗县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安源区',
							'region_area' => '',
						],
						
						[
							'region_name' => '湘东区',
							'region_area' => '',
						],
						
						[
							'region_name' => '芦溪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '莲花县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '九江市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '九江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '修水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '庐山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '彭泽县',
							'region_area' => '',
						],
						
						[
							'region_name' => '德安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '星子县',
							'region_area' => '',
						],
						
						[
							'region_name' => '武宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永修县',
							'region_area' => '',
						],
						
						[
							'region_name' => '浔阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '湖口县',
							'region_area' => '',
						],
						
						[
							'region_name' => '瑞昌市',
							'region_area' => '',
						],
						
						[
							'region_name' => '都昌县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '新余市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '分宜县',
							'region_area' => '',
						],
						
						[
							'region_name' => '渝水区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '鹰潭市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '余江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '月湖区',
							'region_area' => '',
						],
						
						[
							'region_name' => '贵溪市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '赣州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '上犹县',
							'region_area' => '',
						],
						
						[
							'region_name' => '于都县',
							'region_area' => '',
						],
						
						[
							'region_name' => '会昌县',
							'region_area' => '',
						],
						
						[
							'region_name' => '信丰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '全南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '兴国县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南康市',
							'region_area' => '',
						],
						
						[
							'region_name' => '大余县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宁都县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安远县',
							'region_area' => '',
						],
						
						[
							'region_name' => '定南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '寻乌县',
							'region_area' => '',
						],
						
						[
							'region_name' => '崇义县',
							'region_area' => '',
						],
						
						[
							'region_name' => '瑞金市',
							'region_area' => '',
						],
						
						[
							'region_name' => '石城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '章贡区',
							'region_area' => '',
						],
						
						[
							'region_name' => '赣县',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙南县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '吉安市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '万安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '井冈山市',
							'region_area' => '',
						],
						
						[
							'region_name' => '吉安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '吉州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '吉水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安福县',
							'region_area' => '',
						],
						
						[
							'region_name' => '峡江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新干县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永丰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永新县',
							'region_area' => '',
						],
						
						[
							'region_name' => '泰和县',
							'region_area' => '',
						],
						
						[
							'region_name' => '遂川县',
							'region_area' => '',
						],
						
						[
							'region_name' => '青原区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '宜春市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '万载县',
							'region_area' => '',
						],
						
						[
							'region_name' => '上高县',
							'region_area' => '',
						],
						
						[
							'region_name' => '丰城市',
							'region_area' => '',
						],
						
						[
							'region_name' => '奉新县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宜丰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '樟树市',
							'region_area' => '',
						],
						
						[
							'region_name' => '袁州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '铜鼓县',
							'region_area' => '',
						],
						
						[
							'region_name' => '靖安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '高安市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '抚州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东乡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '临川区',
							'region_area' => '',
						],
						
						[
							'region_name' => '乐安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南丰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宜黄县',
							'region_area' => '',
						],
						
						[
							'region_name' => '崇仁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '广昌县',
							'region_area' => '',
						],
						
						[
							'region_name' => '资溪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '金溪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '黎川县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '上饶市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '万年县',
							'region_area' => '',
						],
						
						[
							'region_name' => '上饶县',
							'region_area' => '',
						],
						
						[
							'region_name' => '余干县',
							'region_area' => '',
						],
						
						[
							'region_name' => '信州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '婺源县',
							'region_area' => '',
						],
						
						[
							'region_name' => '广丰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '弋阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '德兴市',
							'region_area' => '',
						],
						
						[
							'region_name' => '横峰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '玉山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鄱阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '铅山县',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '山东',
					'region_area' => '华东',
					'children' => 
					[
					
					[
						'region_name' => '济南市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '历下区',
							'region_area' => '',
						],
						
						[
							'region_name' => '历城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '商河县',
							'region_area' => '',
						],
						
						[
							'region_name' => '天桥区',
							'region_area' => '',
						],
						
						[
							'region_name' => '市中区',
							'region_area' => '',
						],
						
						[
							'region_name' => '平阴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '槐荫区',
							'region_area' => '',
						],
						
						[
							'region_name' => '济阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '章丘市',
							'region_area' => '',
						],
						
						[
							'region_name' => '长清区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '青岛市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '即墨市',
							'region_area' => '',
						],
						
						[
							'region_name' => '四方区',
							'region_area' => '',
						],
						
						[
							'region_name' => '城阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '崂山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '市北区',
							'region_area' => '',
						],
						
						[
							'region_name' => '市南区',
							'region_area' => '',
						],
						
						[
							'region_name' => '平度市',
							'region_area' => '',
						],
						
						[
							'region_name' => '李沧区',
							'region_area' => '',
						],
						
						[
							'region_name' => '胶南市',
							'region_area' => '',
						],
						
						[
							'region_name' => '胶州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '莱西市',
							'region_area' => '',
						],
						
						[
							'region_name' => '黄岛区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '淄博市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '临淄区',
							'region_area' => '',
						],
						
						[
							'region_name' => '博山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '周村区',
							'region_area' => '',
						],
						
						[
							'region_name' => '张店区',
							'region_area' => '',
						],
						
						[
							'region_name' => '桓台县',
							'region_area' => '',
						],
						
						[
							'region_name' => '沂源县',
							'region_area' => '',
						],
						
						[
							'region_name' => '淄川区',
							'region_area' => '',
						],
						
						[
							'region_name' => '高青县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '枣庄市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '台儿庄区',
							'region_area' => '',
						],
						
						[
							'region_name' => '山亭区',
							'region_area' => '',
						],
						
						[
							'region_name' => '峄城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '市中区',
							'region_area' => '',
						],
						
						[
							'region_name' => '滕州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '薛城区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '东营市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东营区',
							'region_area' => '',
						],
						
						[
							'region_name' => '利津县',
							'region_area' => '',
						],
						
						[
							'region_name' => '垦利县',
							'region_area' => '',
						],
						
						[
							'region_name' => '广饶县',
							'region_area' => '',
						],
						
						[
							'region_name' => '河口区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '烟台市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '招远市',
							'region_area' => '',
						],
						
						[
							'region_name' => '栖霞市',
							'region_area' => '',
						],
						
						[
							'region_name' => '海阳市',
							'region_area' => '',
						],
						
						[
							'region_name' => '牟平区',
							'region_area' => '',
						],
						
						[
							'region_name' => '福山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '芝罘区',
							'region_area' => '',
						],
						
						[
							'region_name' => '莱山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '莱州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '莱阳市',
							'region_area' => '',
						],
						
						[
							'region_name' => '蓬莱市',
							'region_area' => '',
						],
						
						[
							'region_name' => '长岛县',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙口市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '潍坊市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '临朐县',
							'region_area' => '',
						],
						
						[
							'region_name' => '坊子区',
							'region_area' => '',
						],
						
						[
							'region_name' => '奎文区',
							'region_area' => '',
						],
						
						[
							'region_name' => '安丘市',
							'region_area' => '',
						],
						
						[
							'region_name' => '寒亭区',
							'region_area' => '',
						],
						
						[
							'region_name' => '寿光市',
							'region_area' => '',
						],
						
						[
							'region_name' => '昌乐县',
							'region_area' => '',
						],
						
						[
							'region_name' => '昌邑市',
							'region_area' => '',
						],
						
						[
							'region_name' => '潍城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '诸城市',
							'region_area' => '',
						],
						
						[
							'region_name' => '青州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '高密市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '济宁市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '任城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '兖州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '嘉祥县',
							'region_area' => '',
						],
						
						[
							'region_name' => '市中区',
							'region_area' => '',
						],
						
						[
							'region_name' => '微山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '曲阜市',
							'region_area' => '',
						],
						
						[
							'region_name' => '梁山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '汶上县',
							'region_area' => '',
						],
						
						[
							'region_name' => '泗水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '邹城市',
							'region_area' => '',
						],
						
						[
							'region_name' => '金乡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鱼台县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '泰安市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宁阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '岱岳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '新泰市',
							'region_area' => '',
						],
						
						[
							'region_name' => '泰山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '肥城市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '威海市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '乳山市',
							'region_area' => '',
						],
						
						[
							'region_name' => '文登市',
							'region_area' => '',
						],
						
						[
							'region_name' => '环翠区',
							'region_area' => '',
						],
						
						[
							'region_name' => '荣成市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '日照市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东港区',
							'region_area' => '',
						],
						
						[
							'region_name' => '五莲县',
							'region_area' => '',
						],
						
						[
							'region_name' => '岚山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '莒县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '莱芜市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '莱城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '钢城区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '临沂市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '临沭县',
							'region_area' => '',
						],
						
						[
							'region_name' => '兰山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '平邑县',
							'region_area' => '',
						],
						
						[
							'region_name' => '沂南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '沂水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '河东区',
							'region_area' => '',
						],
						
						[
							'region_name' => '罗庄区',
							'region_area' => '',
						],
						
						[
							'region_name' => '苍山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '莒南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '蒙阴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '费县',
							'region_area' => '',
						],
						
						[
							'region_name' => '郯城县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '德州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '临邑县',
							'region_area' => '',
						],
						
						[
							'region_name' => '乐陵市',
							'region_area' => '',
						],
						
						[
							'region_name' => '夏津县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宁津县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平原县',
							'region_area' => '',
						],
						
						[
							'region_name' => '庆云县',
							'region_area' => '',
						],
						
						[
							'region_name' => '德城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '武城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '禹城市',
							'region_area' => '',
						],
						
						[
							'region_name' => '陵县',
							'region_area' => '',
						],
						
						[
							'region_name' => '齐河县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '聊城市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东昌府区',
							'region_area' => '',
						],
						
						[
							'region_name' => '东阿县',
							'region_area' => '',
						],
						
						[
							'region_name' => '临清市',
							'region_area' => '',
						],
						
						[
							'region_name' => '冠县',
							'region_area' => '',
						],
						
						[
							'region_name' => '茌平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '莘县',
							'region_area' => '',
						],
						
						[
							'region_name' => '阳谷县',
							'region_area' => '',
						],
						
						[
							'region_name' => '高唐县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '滨州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '博兴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '惠民县',
							'region_area' => '',
						],
						
						[
							'region_name' => '无棣县',
							'region_area' => '',
						],
						
						[
							'region_name' => '沾化县',
							'region_area' => '',
						],
						
						[
							'region_name' => '滨城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '邹平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '阳信县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '菏泽市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东明县',
							'region_area' => '',
						],
						
						[
							'region_name' => '单县',
							'region_area' => '',
						],
						
						[
							'region_name' => '定陶县',
							'region_area' => '',
						],
						
						[
							'region_name' => '巨野县',
							'region_area' => '',
						],
						
						[
							'region_name' => '成武县',
							'region_area' => '',
						],
						
						[
							'region_name' => '曹县',
							'region_area' => '',
						],
						
						[
							'region_name' => '牡丹区',
							'region_area' => '',
						],
						
						[
							'region_name' => '郓城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鄄城县',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '河南',
					'region_area' => '华中',
					'children' => 
					[
					
					[
						'region_name' => '郑州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '上街区',
							'region_area' => '',
						],
						
						[
							'region_name' => '中原区',
							'region_area' => '',
						],
						
						[
							'region_name' => '中牟县',
							'region_area' => '',
						],
						
						[
							'region_name' => '二七区',
							'region_area' => '',
						],
						
						[
							'region_name' => '巩义市',
							'region_area' => '',
						],
						
						[
							'region_name' => '惠济区',
							'region_area' => '',
						],
						
						[
							'region_name' => '新密市',
							'region_area' => '',
						],
						
						[
							'region_name' => '新郑市',
							'region_area' => '',
						],
						
						[
							'region_name' => '登封市',
							'region_area' => '',
						],
						
						[
							'region_name' => '管城回族区',
							'region_area' => '',
						],
						
						[
							'region_name' => '荥阳市',
							'region_area' => '',
						],
						
						[
							'region_name' => '金水区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '开封市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '兰考县',
							'region_area' => '',
						],
						
						[
							'region_name' => '尉氏县',
							'region_area' => '',
						],
						
						[
							'region_name' => '开封县',
							'region_area' => '',
						],
						
						[
							'region_name' => '杞县',
							'region_area' => '',
						],
						
						[
							'region_name' => '禹王台区',
							'region_area' => '',
						],
						
						[
							'region_name' => '通许县',
							'region_area' => '',
						],
						
						[
							'region_name' => '金明区',
							'region_area' => '',
						],
						
						[
							'region_name' => '顺河回族区',
							'region_area' => '',
						],
						
						[
							'region_name' => '鼓楼区',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙亭区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '洛阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '伊川县',
							'region_area' => '',
						],
						
						[
							'region_name' => '偃师市',
							'region_area' => '',
						],
						
						[
							'region_name' => '吉利区',
							'region_area' => '',
						],
						
						[
							'region_name' => '孟津县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宜阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '嵩县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '栾川县',
							'region_area' => '',
						],
						
						[
							'region_name' => '汝阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '洛宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '洛龙区',
							'region_area' => '',
						],
						
						[
							'region_name' => '涧西区',
							'region_area' => '',
						],
						
						[
							'region_name' => '瀍河回族区',
							'region_area' => '',
						],
						
						[
							'region_name' => '老城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '西工区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '平顶山市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '卫东区',
							'region_area' => '',
						],
						
						[
							'region_name' => '叶县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宝丰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新华区',
							'region_area' => '',
						],
						
						[
							'region_name' => '汝州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '湛河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '石龙区',
							'region_area' => '',
						],
						
						[
							'region_name' => '舞钢市',
							'region_area' => '',
						],
						
						[
							'region_name' => '郏县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鲁山县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '安阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '内黄县',
							'region_area' => '',
						],
						
						[
							'region_name' => '北关区',
							'region_area' => '',
						],
						
						[
							'region_name' => '安阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '文峰区',
							'region_area' => '',
						],
						
						[
							'region_name' => '林州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '殷都区',
							'region_area' => '',
						],
						
						[
							'region_name' => '汤阴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '滑县',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙安区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '鹤壁市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '山城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '浚县',
							'region_area' => '',
						],
						
						[
							'region_name' => '淇县',
							'region_area' => '',
						],
						
						[
							'region_name' => '淇滨区',
							'region_area' => '',
						],
						
						[
							'region_name' => '鹤山区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '新乡市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '凤泉区',
							'region_area' => '',
						],
						
						[
							'region_name' => '卫滨区',
							'region_area' => '',
						],
						
						[
							'region_name' => '卫辉市',
							'region_area' => '',
						],
						
						[
							'region_name' => '原阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '封丘县',
							'region_area' => '',
						],
						
						[
							'region_name' => '延津县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新乡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '牧野区',
							'region_area' => '',
						],
						
						[
							'region_name' => '红旗区',
							'region_area' => '',
						],
						
						[
							'region_name' => '获嘉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '辉县市',
							'region_area' => '',
						],
						
						[
							'region_name' => '长垣县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '焦作市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '中站区',
							'region_area' => '',
						],
						
						[
							'region_name' => '修武县',
							'region_area' => '',
						],
						
						[
							'region_name' => '博爱县',
							'region_area' => '',
						],
						
						[
							'region_name' => '孟州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '山阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '武陟县',
							'region_area' => '',
						],
						
						[
							'region_name' => '沁阳市',
							'region_area' => '',
						],
						
						[
							'region_name' => '温县',
							'region_area' => '',
						],
						
						[
							'region_name' => '解放区',
							'region_area' => '',
						],
						
						[
							'region_name' => '马村区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '濮阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '华龙区',
							'region_area' => '',
						],
						
						[
							'region_name' => '南乐县',
							'region_area' => '',
						],
						
						[
							'region_name' => '台前县',
							'region_area' => '',
						],
						
						[
							'region_name' => '清丰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '濮阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '范县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '许昌市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '禹州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '襄城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '许昌县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鄢陵县',
							'region_area' => '',
						],
						
						[
							'region_name' => '长葛市',
							'region_area' => '',
						],
						
						[
							'region_name' => '魏都区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '漯河市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '临颍县',
							'region_area' => '',
						],
						
						[
							'region_name' => '召陵区',
							'region_area' => '',
						],
						
						[
							'region_name' => '源汇区',
							'region_area' => '',
						],
						
						[
							'region_name' => '舞阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '郾城区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '三门峡市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '义马市',
							'region_area' => '',
						],
						
						[
							'region_name' => '卢氏县',
							'region_area' => '',
						],
						
						[
							'region_name' => '渑池县',
							'region_area' => '',
						],
						
						[
							'region_name' => '湖滨区',
							'region_area' => '',
						],
						
						[
							'region_name' => '灵宝市',
							'region_area' => '',
						],
						
						[
							'region_name' => '陕县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '南阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '内乡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南召县',
							'region_area' => '',
						],
						
						[
							'region_name' => '卧龙区',
							'region_area' => '',
						],
						
						[
							'region_name' => '唐河县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宛城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '新野县',
							'region_area' => '',
						],
						
						[
							'region_name' => '方城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '桐柏县',
							'region_area' => '',
						],
						
						[
							'region_name' => '淅川县',
							'region_area' => '',
						],
						
						[
							'region_name' => '社旗县',
							'region_area' => '',
						],
						
						[
							'region_name' => '西峡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '邓州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '镇平县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '商丘市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '夏邑县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宁陵县',
							'region_area' => '',
						],
						
						[
							'region_name' => '柘城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '民权县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永城市',
							'region_area' => '',
						],
						
						[
							'region_name' => '睢县',
							'region_area' => '',
						],
						
						[
							'region_name' => '睢阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '粱园区',
							'region_area' => '',
						],
						
						[
							'region_name' => '虞城县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '信阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '光山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '商城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '固始县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平桥区',
							'region_area' => '',
						],
						
						[
							'region_name' => '息县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新县',
							'region_area' => '',
						],
						
						[
							'region_name' => '浉河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '淮滨县',
							'region_area' => '',
						],
						
						[
							'region_name' => '潢川县',
							'region_area' => '',
						],
						
						[
							'region_name' => '罗山县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '周口市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '商水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '太康县',
							'region_area' => '',
						],
						
						[
							'region_name' => '川汇区',
							'region_area' => '',
						],
						
						[
							'region_name' => '扶沟县',
							'region_area' => '',
						],
						
						[
							'region_name' => '沈丘县',
							'region_area' => '',
						],
						
						[
							'region_name' => '淮阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '西华县',
							'region_area' => '',
						],
						
						[
							'region_name' => '郸城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '项城市',
							'region_area' => '',
						],
						
						[
							'region_name' => '鹿邑县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '驻马店市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '上蔡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平舆县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新蔡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '正阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '汝南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '泌阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '确山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '西平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '遂平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '驿城区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '济源市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '济源市',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '湖北',
					'region_area' => '华中',
					'children' => 
					[
					
					[
						'region_name' => '武汉市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东西湖区',
							'region_area' => '',
						],
						
						[
							'region_name' => '新洲区',
							'region_area' => '',
						],
						
						[
							'region_name' => '武昌区',
							'region_area' => '',
						],
						
						[
							'region_name' => '汉南区',
							'region_area' => '',
						],
						
						[
							'region_name' => '汉阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '江夏区',
							'region_area' => '',
						],
						
						[
							'region_name' => '江岸区',
							'region_area' => '',
						],
						
						[
							'region_name' => '江汉区',
							'region_area' => '',
						],
						
						[
							'region_name' => '洪山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '硚口区',
							'region_area' => '',
						],
						
						[
							'region_name' => '蔡甸区',
							'region_area' => '',
						],
						
						[
							'region_name' => '青山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '黄陂区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '黄石市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '下陆区',
							'region_area' => '',
						],
						
						[
							'region_name' => '大冶市',
							'region_area' => '',
						],
						
						[
							'region_name' => '西塞山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '铁山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '阳新县',
							'region_area' => '',
						],
						
						[
							'region_name' => '黄石港区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '十堰市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '丹江口市',
							'region_area' => '',
						],
						
						[
							'region_name' => '张湾区',
							'region_area' => '',
						],
						
						[
							'region_name' => '房县',
							'region_area' => '',
						],
						
						[
							'region_name' => '竹山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '竹溪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '茅箭区',
							'region_area' => '',
						],
						
						[
							'region_name' => '郧县',
							'region_area' => '',
						],
						
						[
							'region_name' => '郧西县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '宜昌市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '五峰土家族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '伍家岗区',
							'region_area' => '',
						],
						
						[
							'region_name' => '兴山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '夷陵区',
							'region_area' => '',
						],
						
						[
							'region_name' => '宜都市',
							'region_area' => '',
						],
						
						[
							'region_name' => '当阳市',
							'region_area' => '',
						],
						
						[
							'region_name' => '枝江市',
							'region_area' => '',
						],
						
						[
							'region_name' => '点军区',
							'region_area' => '',
						],
						
						[
							'region_name' => '秭归县',
							'region_area' => '',
						],
						
						[
							'region_name' => '虢亭区',
							'region_area' => '',
						],
						
						[
							'region_name' => '西陵区',
							'region_area' => '',
						],
						
						[
							'region_name' => '远安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '长阳土家族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '襄樊市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '保康县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南漳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宜城市',
							'region_area' => '',
						],
						
						[
							'region_name' => '枣阳市',
							'region_area' => '',
						],
						
						[
							'region_name' => '樊城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '老河口市',
							'region_area' => '',
						],
						
						[
							'region_name' => '襄城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '襄阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '谷城县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '鄂州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '华容区',
							'region_area' => '',
						],
						
						[
							'region_name' => '粱子湖',
							'region_area' => '',
						],
						
						[
							'region_name' => '鄂城区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '荆门市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东宝区',
							'region_area' => '',
						],
						
						[
							'region_name' => '京山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '掇刀区',
							'region_area' => '',
						],
						
						[
							'region_name' => '沙洋县',
							'region_area' => '',
						],
						
						[
							'region_name' => '钟祥市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '孝感市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '云梦县',
							'region_area' => '',
						],
						
						[
							'region_name' => '大悟县',
							'region_area' => '',
						],
						
						[
							'region_name' => '孝南区',
							'region_area' => '',
						],
						
						[
							'region_name' => '孝昌县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安陆市',
							'region_area' => '',
						],
						
						[
							'region_name' => '应城市',
							'region_area' => '',
						],
						
						[
							'region_name' => '汉川市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '荆州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '公安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '松滋市',
							'region_area' => '',
						],
						
						[
							'region_name' => '江陵县',
							'region_area' => '',
						],
						
						[
							'region_name' => '沙市区',
							'region_area' => '',
						],
						
						[
							'region_name' => '洪湖市',
							'region_area' => '',
						],
						
						[
							'region_name' => '监利县',
							'region_area' => '',
						],
						
						[
							'region_name' => '石首市',
							'region_area' => '',
						],
						
						[
							'region_name' => '荆州区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '黄冈市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '团风县',
							'region_area' => '',
						],
						
						[
							'region_name' => '武穴市',
							'region_area' => '',
						],
						
						[
							'region_name' => '浠水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '红安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '罗田县',
							'region_area' => '',
						],
						
						[
							'region_name' => '英山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '蕲春县',
							'region_area' => '',
						],
						
						[
							'region_name' => '麻城市',
							'region_area' => '',
						],
						
						[
							'region_name' => '黄州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '黄梅县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '咸宁市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '咸安区',
							'region_area' => '',
						],
						
						[
							'region_name' => '嘉鱼县',
							'region_area' => '',
						],
						
						[
							'region_name' => '崇阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '赤壁市',
							'region_area' => '',
						],
						
						[
							'region_name' => '通城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '通山县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '随州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '广水市',
							'region_area' => '',
						],
						
						[
							'region_name' => '曾都区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '恩施土家族苗族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '利川市',
							'region_area' => '',
						],
						
						[
							'region_name' => '咸丰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宣恩县',
							'region_area' => '',
						],
						
						[
							'region_name' => '巴东县',
							'region_area' => '',
						],
						
						[
							'region_name' => '建始县',
							'region_area' => '',
						],
						
						[
							'region_name' => '恩施市',
							'region_area' => '',
						],
						
						[
							'region_name' => '来凤县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鹤峰县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '仙桃市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '仙桃市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '潜江市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '潜江市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '天门市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '天门市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '神农架林区',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '神农架林区',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '湖南',
					'region_area' => '华中',
					'children' => 
					[
					
					[
						'region_name' => '长沙市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '天心区',
							'region_area' => '',
						],
						
						[
							'region_name' => '宁乡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '岳麓区',
							'region_area' => '',
						],
						
						[
							'region_name' => '开福区',
							'region_area' => '',
						],
						
						[
							'region_name' => '望城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '浏阳市',
							'region_area' => '',
						],
						
						[
							'region_name' => '芙蓉区',
							'region_area' => '',
						],
						
						[
							'region_name' => '长沙县',
							'region_area' => '',
						],
						
						[
							'region_name' => '雨花区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '株洲市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '天元区',
							'region_area' => '',
						],
						
						[
							'region_name' => '攸县',
							'region_area' => '',
						],
						
						[
							'region_name' => '株洲县',
							'region_area' => '',
						],
						
						[
							'region_name' => '炎陵县',
							'region_area' => '',
						],
						
						[
							'region_name' => '石峰区',
							'region_area' => '',
						],
						
						[
							'region_name' => '芦淞区',
							'region_area' => '',
						],
						
						[
							'region_name' => '茶陵县',
							'region_area' => '',
						],
						
						[
							'region_name' => '荷塘区',
							'region_area' => '',
						],
						
						[
							'region_name' => '醴陵市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '湘潭市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '岳塘区',
							'region_area' => '',
						],
						
						[
							'region_name' => '湘乡市',
							'region_area' => '',
						],
						
						[
							'region_name' => '湘潭县',
							'region_area' => '',
						],
						
						[
							'region_name' => '雨湖区',
							'region_area' => '',
						],
						
						[
							'region_name' => '韶山市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '衡阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '南岳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '常宁市',
							'region_area' => '',
						],
						
						[
							'region_name' => '珠晖区',
							'region_area' => '',
						],
						
						[
							'region_name' => '石鼓区',
							'region_area' => '',
						],
						
						[
							'region_name' => '祁东县',
							'region_area' => '',
						],
						
						[
							'region_name' => '耒阳市',
							'region_area' => '',
						],
						
						[
							'region_name' => '蒸湘区',
							'region_area' => '',
						],
						
						[
							'region_name' => '衡东县',
							'region_area' => '',
						],
						
						[
							'region_name' => '衡南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '衡山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '衡阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '雁峰区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '邵阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '北塔区',
							'region_area' => '',
						],
						
						[
							'region_name' => '双清区',
							'region_area' => '',
						],
						
						[
							'region_name' => '城步苗族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '大祥区',
							'region_area' => '',
						],
						
						[
							'region_name' => '新宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新邵县',
							'region_area' => '',
						],
						
						[
							'region_name' => '武冈市',
							'region_area' => '',
						],
						
						[
							'region_name' => '洞口县',
							'region_area' => '',
						],
						
						[
							'region_name' => '绥宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '邵东县',
							'region_area' => '',
						],
						
						[
							'region_name' => '邵阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '隆回县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '岳阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '临湘市',
							'region_area' => '',
						],
						
						[
							'region_name' => '云溪区',
							'region_area' => '',
						],
						
						[
							'region_name' => '华容县',
							'region_area' => '',
						],
						
						[
							'region_name' => '君山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '岳阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '岳阳楼区',
							'region_area' => '',
						],
						
						[
							'region_name' => '平江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '汨罗市',
							'region_area' => '',
						],
						
						[
							'region_name' => '湘阴县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '常德市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '临澧县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安乡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '桃源县',
							'region_area' => '',
						],
						
						[
							'region_name' => '武陵区',
							'region_area' => '',
						],
						
						[
							'region_name' => '汉寿县',
							'region_area' => '',
						],
						
						[
							'region_name' => '津市市',
							'region_area' => '',
						],
						
						[
							'region_name' => '澧县',
							'region_area' => '',
						],
						
						[
							'region_name' => '石门县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鼎城区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '张家界市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '慈利县',
							'region_area' => '',
						],
						
						[
							'region_name' => '桑植县',
							'region_area' => '',
						],
						
						[
							'region_name' => '武陵源区',
							'region_area' => '',
						],
						
						[
							'region_name' => '永定区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '益阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安化县',
							'region_area' => '',
						],
						
						[
							'region_name' => '桃江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '沅江市',
							'region_area' => '',
						],
						
						[
							'region_name' => '资阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '赫山区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '郴州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '临武县',
							'region_area' => '',
						],
						
						[
							'region_name' => '北湖区',
							'region_area' => '',
						],
						
						[
							'region_name' => '嘉禾县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安仁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宜章县',
							'region_area' => '',
						],
						
						[
							'region_name' => '桂东县',
							'region_area' => '',
						],
						
						[
							'region_name' => '桂阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永兴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '汝城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '苏仙区',
							'region_area' => '',
						],
						
						[
							'region_name' => '资兴市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '永州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '冷水滩区',
							'region_area' => '',
						],
						
						[
							'region_name' => '双牌县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宁远县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新田县',
							'region_area' => '',
						],
						
						[
							'region_name' => '江华瑶族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '江永县',
							'region_area' => '',
						],
						
						[
							'region_name' => '祁阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '蓝山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '道县',
							'region_area' => '',
						],
						
						[
							'region_name' => '零陵区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '怀化市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '中方县',
							'region_area' => '',
						],
						
						[
							'region_name' => '会同县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新晃侗族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '沅陵县',
							'region_area' => '',
						],
						
						[
							'region_name' => '洪江市/洪江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '溆浦县',
							'region_area' => '',
						],
						
						[
							'region_name' => '芷江侗族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '辰溪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '通道侗族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '靖州苗族侗族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鹤城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '麻阳苗族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '娄底市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '冷水江市',
							'region_area' => '',
						],
						
						[
							'region_name' => '双峰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '娄星区',
							'region_area' => '',
						],
						
						[
							'region_name' => '新化县',
							'region_area' => '',
						],
						
						[
							'region_name' => '涟源市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '湘西土家族苗族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '保靖县',
							'region_area' => '',
						],
						
						[
							'region_name' => '凤凰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '古丈县',
							'region_area' => '',
						],
						
						[
							'region_name' => '吉首市',
							'region_area' => '',
						],
						
						[
							'region_name' => '永顺县',
							'region_area' => '',
						],
						
						[
							'region_name' => '泸溪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '花垣县',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙山县',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '广东',
					'region_area' => '华南',
					'children' => 
					[
					
					[
						'region_name' => '广州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '萝岗区',
							'region_area' => '',
						],
						
						[
							'region_name' => '南沙区',
							'region_area' => '',
						],
						
						[
							'region_name' => '从化市',
							'region_area' => '',
						],
						
						[
							'region_name' => '增城市',
							'region_area' => '',
						],
						
						[
							'region_name' => '天河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '海珠区',
							'region_area' => '',
						],
						
						[
							'region_name' => '番禺区',
							'region_area' => '',
						],
						
						[
							'region_name' => '白云区',
							'region_area' => '',
						],
						
						[
							'region_name' => '花都区',
							'region_area' => '',
						],
						
						[
							'region_name' => '荔湾区',
							'region_area' => '',
						],
						
						[
							'region_name' => '越秀区',
							'region_area' => '',
						],
						
						[
							'region_name' => '黄埔区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '韶关市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '乐昌市',
							'region_area' => '',
						],
						
						[
							'region_name' => '乳源瑶族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '仁化县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南雄市',
							'region_area' => '',
						],
						
						[
							'region_name' => '始兴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新丰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '曲江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '武江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '浈江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '翁源县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '深圳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '南山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '宝安区',
							'region_area' => '',
						],
						
						[
							'region_name' => '盐田区',
							'region_area' => '',
						],
						
						[
							'region_name' => '福田区',
							'region_area' => '',
						],
						
						[
							'region_name' => '罗湖区',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙岗区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '珠海市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '斗门区',
							'region_area' => '',
						],
						
						[
							'region_name' => '金湾区',
							'region_area' => '',
						],
						
						[
							'region_name' => '香洲区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '汕头市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '南澳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '潮南区',
							'region_area' => '',
						],
						
						[
							'region_name' => '潮阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '澄海区',
							'region_area' => '',
						],
						
						[
							'region_name' => '濠江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '金平区',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙湖区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '佛山市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '三水区',
							'region_area' => '',
						],
						
						[
							'region_name' => '南海区',
							'region_area' => '',
						],
						
						[
							'region_name' => '禅城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '顺德区',
							'region_area' => '',
						],
						
						[
							'region_name' => '高明区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '江门市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '台山市',
							'region_area' => '',
						],
						
						[
							'region_name' => '开平市',
							'region_area' => '',
						],
						
						[
							'region_name' => '恩平市',
							'region_area' => '',
						],
						
						[
							'region_name' => '新会区',
							'region_area' => '',
						],
						
						[
							'region_name' => '江海区',
							'region_area' => '',
						],
						
						[
							'region_name' => '蓬江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '鹤山市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '湛江市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '吴川市',
							'region_area' => '',
						],
						
						[
							'region_name' => '坡头区',
							'region_area' => '',
						],
						
						[
							'region_name' => '廉江市',
							'region_area' => '',
						],
						
						[
							'region_name' => '徐闻县',
							'region_area' => '',
						],
						
						[
							'region_name' => '赤坎区',
							'region_area' => '',
						],
						
						[
							'region_name' => '遂溪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '雷州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '霞山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '麻章区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '茂名市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '信宜市',
							'region_area' => '',
						],
						
						[
							'region_name' => '化州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '电白县',
							'region_area' => '',
						],
						
						[
							'region_name' => '茂南区',
							'region_area' => '',
						],
						
						[
							'region_name' => '茂港区',
							'region_area' => '',
						],
						
						[
							'region_name' => '高州市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '肇庆市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '四会市',
							'region_area' => '',
						],
						
						[
							'region_name' => '封开县',
							'region_area' => '',
						],
						
						[
							'region_name' => '广宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '德庆县',
							'region_area' => '',
						],
						
						[
							'region_name' => '怀集县',
							'region_area' => '',
						],
						
						[
							'region_name' => '端州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '高要市',
							'region_area' => '',
						],
						
						[
							'region_name' => '鼎湖区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '惠州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '博罗县',
							'region_area' => '',
						],
						
						[
							'region_name' => '惠东县',
							'region_area' => '',
						],
						
						[
							'region_name' => '惠城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '惠阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙门县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '梅州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '丰顺县',
							'region_area' => '',
						],
						
						[
							'region_name' => '五华县',
							'region_area' => '',
						],
						
						[
							'region_name' => '兴宁市',
							'region_area' => '',
						],
						
						[
							'region_name' => '大埔县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平远县',
							'region_area' => '',
						],
						
						[
							'region_name' => '梅县',
							'region_area' => '',
						],
						
						[
							'region_name' => '梅江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '蕉岭县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '汕尾市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '海丰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '陆丰市',
							'region_area' => '',
						],
						
						[
							'region_name' => '陆河县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '河源市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东源县',
							'region_area' => '',
						],
						
						[
							'region_name' => '和平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '源城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '紫金县',
							'region_area' => '',
						],
						
						[
							'region_name' => '连平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙川县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '阳江市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '江城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '阳东县',
							'region_area' => '',
						],
						
						[
							'region_name' => '阳春市',
							'region_area' => '',
						],
						
						[
							'region_name' => '阳西县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '清远市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '佛冈县',
							'region_area' => '',
						],
						
						[
							'region_name' => '清城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '清新县',
							'region_area' => '',
						],
						
						[
							'region_name' => '英德市',
							'region_area' => '',
						],
						
						[
							'region_name' => '连南瑶族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '连山壮族瑶族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '连州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '阳山县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '东莞市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东莞市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '中山市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '中山市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '潮州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '湘桥区',
							'region_area' => '',
						],
						
						[
							'region_name' => '潮安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '饶平县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '揭阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '惠来县',
							'region_area' => '',
						],
						
						[
							'region_name' => '揭东县',
							'region_area' => '',
						],
						
						[
							'region_name' => '揭西县',
							'region_area' => '',
						],
						
						[
							'region_name' => '普宁市',
							'region_area' => '',
						],
						
						[
							'region_name' => '榕城区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '云浮市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '云城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '云安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新兴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '罗定市',
							'region_area' => '',
						],
						
						[
							'region_name' => '郁南县',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '广西',
					'region_area' => '华南',
					'children' => 
					[
					
					[
						'region_name' => '南宁市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '上林县',
							'region_area' => '',
						],
						
						[
							'region_name' => '兴宁区',
							'region_area' => '',
						],
						
						[
							'region_name' => '宾阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '横县',
							'region_area' => '',
						],
						
						[
							'region_name' => '武鸣县',
							'region_area' => '',
						],
						
						[
							'region_name' => '江南区',
							'region_area' => '',
						],
						
						[
							'region_name' => '良庆区',
							'region_area' => '',
						],
						
						[
							'region_name' => '西乡塘区',
							'region_area' => '',
						],
						
						[
							'region_name' => '邕宁区',
							'region_area' => '',
						],
						
						[
							'region_name' => '隆安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '青秀区',
							'region_area' => '',
						],
						
						[
							'region_name' => '马山县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '柳州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '三江侗族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '城中区',
							'region_area' => '',
						],
						
						[
							'region_name' => '柳北区',
							'region_area' => '',
						],
						
						[
							'region_name' => '柳南区',
							'region_area' => '',
						],
						
						[
							'region_name' => '柳城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '柳江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '融安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '融水苗族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鱼峰区',
							'region_area' => '',
						],
						
						[
							'region_name' => '鹿寨县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '桂林市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '七星区',
							'region_area' => '',
						],
						
						[
							'region_name' => '临桂县',
							'region_area' => '',
						],
						
						[
							'region_name' => '全州县',
							'region_area' => '',
						],
						
						[
							'region_name' => '兴安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '叠彩区',
							'region_area' => '',
						],
						
						[
							'region_name' => '平乐县',
							'region_area' => '',
						],
						
						[
							'region_name' => '恭城瑶族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永福县',
							'region_area' => '',
						],
						
						[
							'region_name' => '灌阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '灵川县',
							'region_area' => '',
						],
						
						[
							'region_name' => '秀峰区',
							'region_area' => '',
						],
						
						[
							'region_name' => '荔浦县',
							'region_area' => '',
						],
						
						[
							'region_name' => '象山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '资源县',
							'region_area' => '',
						],
						
						[
							'region_name' => '阳朔县',
							'region_area' => '',
						],
						
						[
							'region_name' => '雁山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙胜各族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '梧州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '万秀区',
							'region_area' => '',
						],
						
						[
							'region_name' => '岑溪市',
							'region_area' => '',
						],
						
						[
							'region_name' => '苍梧县',
							'region_area' => '',
						],
						
						[
							'region_name' => '蒙山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '藤县',
							'region_area' => '',
						],
						
						[
							'region_name' => '蝶山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '长洲区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '北海市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '合浦县',
							'region_area' => '',
						],
						
						[
							'region_name' => '海城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '铁山港区',
							'region_area' => '',
						],
						
						[
							'region_name' => '银海区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '防城港市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '上思县',
							'region_area' => '',
						],
						
						[
							'region_name' => '东兴市',
							'region_area' => '',
						],
						
						[
							'region_name' => '港口区',
							'region_area' => '',
						],
						
						[
							'region_name' => '防城区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '钦州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '浦北县',
							'region_area' => '',
						],
						
						[
							'region_name' => '灵山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '钦北区',
							'region_area' => '',
						],
						
						[
							'region_name' => '钦南区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '贵港市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '平南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '桂平市',
							'region_area' => '',
						],
						
						[
							'region_name' => '港北区',
							'region_area' => '',
						],
						
						[
							'region_name' => '港南区',
							'region_area' => '',
						],
						
						[
							'region_name' => '覃塘区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '玉林市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '兴业县',
							'region_area' => '',
						],
						
						[
							'region_name' => '北流市',
							'region_area' => '',
						],
						
						[
							'region_name' => '博白县',
							'region_area' => '',
						],
						
						[
							'region_name' => '容县',
							'region_area' => '',
						],
						
						[
							'region_name' => '玉州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '陆川县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '百色市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '乐业县',
							'region_area' => '',
						],
						
						[
							'region_name' => '凌云县',
							'region_area' => '',
						],
						
						[
							'region_name' => '右江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '平果县',
							'region_area' => '',
						],
						
						[
							'region_name' => '德保县',
							'region_area' => '',
						],
						
						[
							'region_name' => '田东县',
							'region_area' => '',
						],
						
						[
							'region_name' => '田林县',
							'region_area' => '',
						],
						
						[
							'region_name' => '田阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '西林县',
							'region_area' => '',
						],
						
						[
							'region_name' => '那坡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '隆林各族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '靖西县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '贺州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '八步区',
							'region_area' => '',
						],
						
						[
							'region_name' => '富川瑶族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '昭平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '钟山县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '河池市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东兰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '凤山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南丹县',
							'region_area' => '',
						],
						
						[
							'region_name' => '大化瑶族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '天峨县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宜州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '巴马瑶族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '环江毛南族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '罗城仫佬族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '都安瑶族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '金城江区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '来宾市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '兴宾区',
							'region_area' => '',
						],
						
						[
							'region_name' => '合山市',
							'region_area' => '',
						],
						
						[
							'region_name' => '忻城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '武宣县',
							'region_area' => '',
						],
						
						[
							'region_name' => '象州县',
							'region_area' => '',
						],
						
						[
							'region_name' => '金秀瑶族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '崇左市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '凭祥市',
							'region_area' => '',
						],
						
						[
							'region_name' => '大新县',
							'region_area' => '',
						],
						
						[
							'region_name' => '天等县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宁明县',
							'region_area' => '',
						],
						
						[
							'region_name' => '扶绥县',
							'region_area' => '',
						],
						
						[
							'region_name' => '江州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙州县',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '海南',
					'region_area' => '华南',
					'children' => 
					[
					
					[
						'region_name' => '海口市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '琼山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '秀英区',
							'region_area' => '',
						],
						
						[
							'region_name' => '美兰区',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙华区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '三亚市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '三亚市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '五指山市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '五指山市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '琼海市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '琼海市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '儋州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '儋州市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '文昌市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '文昌市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '万宁市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '万宁市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '东方市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东方市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '定安县',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '定安县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '屯昌县',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '屯昌县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '澄迈县',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '澄迈县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '临高县',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '临高县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '白沙黎族自治县',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '白沙黎族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '昌江黎族自治县',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '昌江黎族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '乐东黎族自治县',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '乐东黎族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '陵水黎族自治县',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '陵水黎族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '保亭黎族苗族自治县',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '保亭黎族苗族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '琼中黎族苗族自治县',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '琼中黎族苗族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '西沙群岛',
						'region_area' => '',
					],
					
					[
						'region_name' => '南沙群岛',
						'region_area' => '',
					],
					
					[
						'region_name' => '中沙群岛的岛礁及其海域',
						'region_area' => '',
					],
					],
				],
				
				[
					'region_name' => '重庆',
					'region_area' => '西南',
					'children' => 
					[
					
					[
						'region_name' => '重庆市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '万州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '涪陵区',
							'region_area' => '',
						],
						
						[
							'region_name' => '渝中区',
							'region_area' => '',
						],
						
						[
							'region_name' => '大渡口区',
							'region_area' => '',
						],
						
						[
							'region_name' => '江北区',
							'region_area' => '',
						],
						
						[
							'region_name' => '沙坪坝区',
							'region_area' => '',
						],
						
						[
							'region_name' => '九龙坡区',
							'region_area' => '',
						],
						
						[
							'region_name' => '南岸区',
							'region_area' => '',
						],
						
						[
							'region_name' => '北碚区',
							'region_area' => '',
						],
						
						[
							'region_name' => '双桥区',
							'region_area' => '',
						],
						
						[
							'region_name' => '万盛区',
							'region_area' => '',
						],
						
						[
							'region_name' => '渝北区',
							'region_area' => '',
						],
						
						[
							'region_name' => '巴南区',
							'region_area' => '',
						],
						
						[
							'region_name' => '黔江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '长寿区',
							'region_area' => '',
						],
						
						[
							'region_name' => '綦江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '潼南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '铜梁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '大足县',
							'region_area' => '',
						],
						
						[
							'region_name' => '荣昌县',
							'region_area' => '',
						],
						
						[
							'region_name' => '璧山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '梁平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '城口县',
							'region_area' => '',
						],
						
						[
							'region_name' => '丰都县',
							'region_area' => '',
						],
						
						[
							'region_name' => '垫江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '武隆县',
							'region_area' => '',
						],
						
						[
							'region_name' => '忠县',
							'region_area' => '',
						],
						
						[
							'region_name' => '开县',
							'region_area' => '',
						],
						
						[
							'region_name' => '云阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '奉节县',
							'region_area' => '',
						],
						
						[
							'region_name' => '巫山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '巫溪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '石柱土家族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '秀山土家族苗族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '酉阳土家族苗族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '彭水苗族土家族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '江津市',
							'region_area' => '',
						],
						
						[
							'region_name' => '合川市',
							'region_area' => '',
						],
						
						[
							'region_name' => '永川市',
							'region_area' => '',
						],
						
						[
							'region_name' => '南川市',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '四川',
					'region_area' => '西南',
					'children' => 
					[
					
					[
						'region_name' => '成都市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '双流县',
							'region_area' => '',
						],
						
						[
							'region_name' => '大邑县',
							'region_area' => '',
						],
						
						[
							'region_name' => '崇州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '彭州市',
							'region_area' => '',
						],
						
						[
							'region_name' => '成华区',
							'region_area' => '',
						],
						
						[
							'region_name' => '新津县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新都区',
							'region_area' => '',
						],
						
						[
							'region_name' => '武侯区',
							'region_area' => '',
						],
						
						[
							'region_name' => '温江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '蒲江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '邛崃市',
							'region_area' => '',
						],
						
						[
							'region_name' => '郫县',
							'region_area' => '',
						],
						
						[
							'region_name' => '都江堰市',
							'region_area' => '',
						],
						
						[
							'region_name' => '金堂县',
							'region_area' => '',
						],
						
						[
							'region_name' => '金牛区',
							'region_area' => '',
						],
						
						[
							'region_name' => '锦江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '青白江区',
							'region_area' => '',
						],
						
						[
							'region_name' => '青羊区',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙泉驿区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '自贡市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '大安区',
							'region_area' => '',
						],
						
						[
							'region_name' => '富顺县',
							'region_area' => '',
						],
						
						[
							'region_name' => '沿滩区',
							'region_area' => '',
						],
						
						[
							'region_name' => '自流井区',
							'region_area' => '',
						],
						
						[
							'region_name' => '荣县',
							'region_area' => '',
						],
						
						[
							'region_name' => '贡井区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '攀枝花市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东区',
							'region_area' => '',
						],
						
						[
							'region_name' => '仁和区',
							'region_area' => '',
						],
						
						[
							'region_name' => '盐边县',
							'region_area' => '',
						],
						
						[
							'region_name' => '米易县',
							'region_area' => '',
						],
						
						[
							'region_name' => '西区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '泸州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '叙永县',
							'region_area' => '',
						],
						
						[
							'region_name' => '古蔺县',
							'region_area' => '',
						],
						
						[
							'region_name' => '合江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '江阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '泸县',
							'region_area' => '',
						],
						
						[
							'region_name' => '纳溪区',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙马潭区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '德阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '中江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '什邡市',
							'region_area' => '',
						],
						
						[
							'region_name' => '广汉市',
							'region_area' => '',
						],
						
						[
							'region_name' => '旌阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '绵竹市',
							'region_area' => '',
						],
						
						[
							'region_name' => '罗江县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '绵阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '三台县',
							'region_area' => '',
						],
						
						[
							'region_name' => '北川羌族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平武县',
							'region_area' => '',
						],
						
						[
							'region_name' => '梓潼县',
							'region_area' => '',
						],
						
						[
							'region_name' => '江油市',
							'region_area' => '',
						],
						
						[
							'region_name' => '涪城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '游仙区',
							'region_area' => '',
						],
						
						[
							'region_name' => '盐亭县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '广元市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '元坝区',
							'region_area' => '',
						],
						
						[
							'region_name' => '利州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '剑阁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '旺苍县',
							'region_area' => '',
						],
						
						[
							'region_name' => '朝天区',
							'region_area' => '',
						],
						
						[
							'region_name' => '苍溪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '青川县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '遂宁市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '大英县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安居区',
							'region_area' => '',
						],
						
						[
							'region_name' => '射洪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '船山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '蓬溪县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '内江市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东兴区',
							'region_area' => '',
						],
						
						[
							'region_name' => '威远县',
							'region_area' => '',
						],
						
						[
							'region_name' => '市中区',
							'region_area' => '',
						],
						
						[
							'region_name' => '资中县',
							'region_area' => '',
						],
						
						[
							'region_name' => '隆昌县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '乐山市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '五通桥区',
							'region_area' => '',
						],
						
						[
							'region_name' => '井研县',
							'region_area' => '',
						],
						
						[
							'region_name' => '夹江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '峨眉山市',
							'region_area' => '',
						],
						
						[
							'region_name' => '峨边彝族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '市中区',
							'region_area' => '',
						],
						
						[
							'region_name' => '沐川县',
							'region_area' => '',
						],
						
						[
							'region_name' => '沙湾区',
							'region_area' => '',
						],
						
						[
							'region_name' => '犍为县',
							'region_area' => '',
						],
						
						[
							'region_name' => '金口河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '马边彝族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '南充市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '仪陇县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南充市嘉陵区',
							'region_area' => '',
						],
						
						[
							'region_name' => '南部县',
							'region_area' => '',
						],
						
						[
							'region_name' => '嘉陵区',
							'region_area' => '',
						],
						
						[
							'region_name' => '营山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '蓬安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '西充县',
							'region_area' => '',
						],
						
						[
							'region_name' => '阆中市',
							'region_area' => '',
						],
						
						[
							'region_name' => '顺庆区',
							'region_area' => '',
						],
						
						[
							'region_name' => '高坪区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '眉山市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东坡区',
							'region_area' => '',
						],
						
						[
							'region_name' => '丹棱县',
							'region_area' => '',
						],
						
						[
							'region_name' => '仁寿县',
							'region_area' => '',
						],
						
						[
							'region_name' => '彭山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '洪雅县',
							'region_area' => '',
						],
						
						[
							'region_name' => '青神县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '宜宾市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '兴文县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南溪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宜宾县',
							'region_area' => '',
						],
						
						[
							'region_name' => '屏山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '江安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '珙县',
							'region_area' => '',
						],
						
						[
							'region_name' => '筠连县',
							'region_area' => '',
						],
						
						[
							'region_name' => '翠屏区',
							'region_area' => '',
						],
						
						[
							'region_name' => '长宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '高县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '广安市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '华蓥市',
							'region_area' => '',
						],
						
						[
							'region_name' => '岳池县',
							'region_area' => '',
						],
						
						[
							'region_name' => '广安区',
							'region_area' => '',
						],
						
						[
							'region_name' => '武胜县',
							'region_area' => '',
						],
						
						[
							'region_name' => '邻水县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '达州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '万源市',
							'region_area' => '',
						],
						
						[
							'region_name' => '大竹县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宣汉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '开江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '渠县',
							'region_area' => '',
						],
						
						[
							'region_name' => '达县',
							'region_area' => '',
						],
						
						[
							'region_name' => '通川区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '雅安市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '名山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '天全县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宝兴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '汉源县',
							'region_area' => '',
						],
						
						[
							'region_name' => '石棉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '芦山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '荥经县',
							'region_area' => '',
						],
						
						[
							'region_name' => '雨城区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '巴中市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '南江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '巴州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '平昌县',
							'region_area' => '',
						],
						
						[
							'region_name' => '通江县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '资阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '乐至县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安岳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '简阳市',
							'region_area' => '',
						],
						
						[
							'region_name' => '雁江区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '阿坝藏族羌族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '九寨沟县',
							'region_area' => '',
						],
						
						[
							'region_name' => '壤塘县',
							'region_area' => '',
						],
						
						[
							'region_name' => '小金县',
							'region_area' => '',
						],
						
						[
							'region_name' => '松潘县',
							'region_area' => '',
						],
						
						[
							'region_name' => '汶川县',
							'region_area' => '',
						],
						
						[
							'region_name' => '理县',
							'region_area' => '',
						],
						
						[
							'region_name' => '红原县',
							'region_area' => '',
						],
						
						[
							'region_name' => '若尔盖县',
							'region_area' => '',
						],
						
						[
							'region_name' => '茂县',
							'region_area' => '',
						],
						
						[
							'region_name' => '金川县',
							'region_area' => '',
						],
						
						[
							'region_name' => '阿坝县',
							'region_area' => '',
						],
						
						[
							'region_name' => '马尔康县',
							'region_area' => '',
						],
						
						[
							'region_name' => '黑水县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '甘孜藏族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '丹巴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '乡城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '巴塘县',
							'region_area' => '',
						],
						
						[
							'region_name' => '康定县',
							'region_area' => '',
						],
						
						[
							'region_name' => '得荣县',
							'region_area' => '',
						],
						
						[
							'region_name' => '德格县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新龙县',
							'region_area' => '',
						],
						
						[
							'region_name' => '泸定县',
							'region_area' => '',
						],
						
						[
							'region_name' => '炉霍县',
							'region_area' => '',
						],
						
						[
							'region_name' => '理塘县',
							'region_area' => '',
						],
						
						[
							'region_name' => '甘孜县',
							'region_area' => '',
						],
						
						[
							'region_name' => '白玉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '石渠县',
							'region_area' => '',
						],
						
						[
							'region_name' => '稻城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '色达县',
							'region_area' => '',
						],
						
						[
							'region_name' => '道孚县',
							'region_area' => '',
						],
						
						[
							'region_name' => '雅江县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '凉山彝族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '会东县',
							'region_area' => '',
						],
						
						[
							'region_name' => '会理县',
							'region_area' => '',
						],
						
						[
							'region_name' => '冕宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '喜德县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宁南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '布拖县',
							'region_area' => '',
						],
						
						[
							'region_name' => '德昌县',
							'region_area' => '',
						],
						
						[
							'region_name' => '昭觉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '普格县',
							'region_area' => '',
						],
						
						[
							'region_name' => '木里藏族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '甘洛县',
							'region_area' => '',
						],
						
						[
							'region_name' => '盐源县',
							'region_area' => '',
						],
						
						[
							'region_name' => '美姑县',
							'region_area' => '',
						],
						
						[
							'region_name' => '西昌',
							'region_area' => '',
						],
						
						[
							'region_name' => '越西县',
							'region_area' => '',
						],
						
						[
							'region_name' => '金阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '雷波县',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '贵州',
					'region_area' => '西南',
					'children' => 
					[
					
					[
						'region_name' => '贵阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '乌当区',
							'region_area' => '',
						],
						
						[
							'region_name' => '云岩区',
							'region_area' => '',
						],
						
						[
							'region_name' => '修文县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南明区',
							'region_area' => '',
						],
						
						[
							'region_name' => '小河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '开阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '息烽县',
							'region_area' => '',
						],
						
						[
							'region_name' => '清镇市',
							'region_area' => '',
						],
						
						[
							'region_name' => '白云区',
							'region_area' => '',
						],
						
						[
							'region_name' => '花溪区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '六盘水市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '六枝特区',
							'region_area' => '',
						],
						
						[
							'region_name' => '水城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '盘县',
							'region_area' => '',
						],
						
						[
							'region_name' => '钟山区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '遵义市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '习水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '仁怀市',
							'region_area' => '',
						],
						
						[
							'region_name' => '余庆县',
							'region_area' => '',
						],
						
						[
							'region_name' => '凤冈县',
							'region_area' => '',
						],
						
						[
							'region_name' => '务川仡佬族苗族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '桐梓县',
							'region_area' => '',
						],
						
						[
							'region_name' => '正安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '汇川区',
							'region_area' => '',
						],
						
						[
							'region_name' => '湄潭县',
							'region_area' => '',
						],
						
						[
							'region_name' => '红花岗区',
							'region_area' => '',
						],
						
						[
							'region_name' => '绥阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '赤水市',
							'region_area' => '',
						],
						
						[
							'region_name' => '道真仡佬族苗族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '遵义县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '安顺市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '关岭布依族苗族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平坝县',
							'region_area' => '',
						],
						
						[
							'region_name' => '普定县',
							'region_area' => '',
						],
						
						[
							'region_name' => '紫云苗族布依族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '西秀区',
							'region_area' => '',
						],
						
						[
							'region_name' => '镇宁布依族苗族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '铜仁地区',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '万山特区',
							'region_area' => '',
						],
						
						[
							'region_name' => '印江土家族苗族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '德江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '思南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '松桃苗族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '江口县',
							'region_area' => '',
						],
						
						[
							'region_name' => '沿河土家族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '玉屏侗族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '石阡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '铜仁市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '黔西南布依族苗族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '兴义市',
							'region_area' => '',
						],
						
						[
							'region_name' => '兴仁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '册亨县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安龙县',
							'region_area' => '',
						],
						
						[
							'region_name' => '普安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '晴隆县',
							'region_area' => '',
						],
						
						[
							'region_name' => '望谟县',
							'region_area' => '',
						],
						
						[
							'region_name' => '贞丰县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '毕节地区',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '大方县',
							'region_area' => '',
						],
						
						[
							'region_name' => '威宁彝族回族苗族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '毕节市',
							'region_area' => '',
						],
						
						[
							'region_name' => '纳雍县',
							'region_area' => '',
						],
						
						[
							'region_name' => '织金县',
							'region_area' => '',
						],
						
						[
							'region_name' => '赫章县',
							'region_area' => '',
						],
						
						[
							'region_name' => '金沙县',
							'region_area' => '',
						],
						
						[
							'region_name' => '黔西县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '黔东南苗族侗族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '三穗县',
							'region_area' => '',
						],
						
						[
							'region_name' => '丹寨县',
							'region_area' => '',
						],
						
						[
							'region_name' => '从江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '凯里市',
							'region_area' => '',
						],
						
						[
							'region_name' => '剑河县',
							'region_area' => '',
						],
						
						[
							'region_name' => '台江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '天柱县',
							'region_area' => '',
						],
						
						[
							'region_name' => '岑巩县',
							'region_area' => '',
						],
						
						[
							'region_name' => '施秉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '榕江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '锦屏县',
							'region_area' => '',
						],
						
						[
							'region_name' => '镇远县',
							'region_area' => '',
						],
						
						[
							'region_name' => '雷山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '麻江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '黄平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '黎平县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '黔南布依族苗族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '三都水族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平塘县',
							'region_area' => '',
						],
						
						[
							'region_name' => '惠水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '独山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '瓮安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '福泉市',
							'region_area' => '',
						],
						
						[
							'region_name' => '罗甸县',
							'region_area' => '',
						],
						
						[
							'region_name' => '荔波县',
							'region_area' => '',
						],
						
						[
							'region_name' => '贵定县',
							'region_area' => '',
						],
						
						[
							'region_name' => '都匀市',
							'region_area' => '',
						],
						
						[
							'region_name' => '长顺县',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙里县',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '云南',
					'region_area' => '西南',
					'children' => 
					[
					
					[
						'region_name' => '昆明市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东川区',
							'region_area' => '',
						],
						
						[
							'region_name' => '五华区',
							'region_area' => '',
						],
						
						[
							'region_name' => '呈贡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安宁市',
							'region_area' => '',
						],
						
						[
							'region_name' => '官渡区',
							'region_area' => '',
						],
						
						[
							'region_name' => '宜良县',
							'region_area' => '',
						],
						
						[
							'region_name' => '富民县',
							'region_area' => '',
						],
						
						[
							'region_name' => '寻甸回族彝族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '嵩明县',
							'region_area' => '',
						],
						
						[
							'region_name' => '晋宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '盘龙区',
							'region_area' => '',
						],
						
						[
							'region_name' => '石林彝族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '禄劝彝族苗族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '西山区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '曲靖市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '会泽县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宣威市',
							'region_area' => '',
						],
						
						[
							'region_name' => '富源县',
							'region_area' => '',
						],
						
						[
							'region_name' => '师宗县',
							'region_area' => '',
						],
						
						[
							'region_name' => '沾益县',
							'region_area' => '',
						],
						
						[
							'region_name' => '罗平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '陆良县',
							'region_area' => '',
						],
						
						[
							'region_name' => '马龙县',
							'region_area' => '',
						],
						
						[
							'region_name' => '麒麟区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '玉溪市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '元江哈尼族彝族傣族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '华宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '峨山彝族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新平彝族傣族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '易门县',
							'region_area' => '',
						],
						
						[
							'region_name' => '江川县',
							'region_area' => '',
						],
						
						[
							'region_name' => '澄江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '红塔区',
							'region_area' => '',
						],
						
						[
							'region_name' => '通海县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '保山市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '施甸县',
							'region_area' => '',
						],
						
						[
							'region_name' => '昌宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '腾冲县',
							'region_area' => '',
						],
						
						[
							'region_name' => '隆阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '龙陵县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '昭通市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '大关县',
							'region_area' => '',
						],
						
						[
							'region_name' => '威信县',
							'region_area' => '',
						],
						
						[
							'region_name' => '巧家县',
							'region_area' => '',
						],
						
						[
							'region_name' => '彝良县',
							'region_area' => '',
						],
						
						[
							'region_name' => '昭阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '水富县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永善县',
							'region_area' => '',
						],
						
						[
							'region_name' => '盐津县',
							'region_area' => '',
						],
						
						[
							'region_name' => '绥江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '镇雄县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鲁甸县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '丽江市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '华坪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '古城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '宁蒗彝族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永胜县',
							'region_area' => '',
						],
						
						[
							'region_name' => '玉龙纳西族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '思茅市',
						'region_area' => '',
					],
					
					[
						'region_name' => '临沧市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '临翔区',
							'region_area' => '',
						],
						
						[
							'region_name' => '云县',
							'region_area' => '',
						],
						
						[
							'region_name' => '凤庆县',
							'region_area' => '',
						],
						
						[
							'region_name' => '双江拉祜族佤族布朗族傣族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永德县',
							'region_area' => '',
						],
						
						[
							'region_name' => '沧源佤族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '耿马傣族佤族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '镇康县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '楚雄彝族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '元谋县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南华县',
							'region_area' => '',
						],
						
						[
							'region_name' => '双柏县',
							'region_area' => '',
						],
						
						[
							'region_name' => '大姚县',
							'region_area' => '',
						],
						
						[
							'region_name' => '姚安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '楚雄市',
							'region_area' => '',
						],
						
						[
							'region_name' => '武定县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永仁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '牟定县',
							'region_area' => '',
						],
						
						[
							'region_name' => '禄丰县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '红河哈尼族彝族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '个旧市',
							'region_area' => '',
						],
						
						[
							'region_name' => '元阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '屏边苗族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '建水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '开远市',
							'region_area' => '',
						],
						
						[
							'region_name' => '弥勒县',
							'region_area' => '',
						],
						
						[
							'region_name' => '河口瑶族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '泸西县',
							'region_area' => '',
						],
						
						[
							'region_name' => '石屏县',
							'region_area' => '',
						],
						
						[
							'region_name' => '红河县',
							'region_area' => '',
						],
						
						[
							'region_name' => '绿春县',
							'region_area' => '',
						],
						
						[
							'region_name' => '蒙自县',
							'region_area' => '',
						],
						
						[
							'region_name' => '金平苗族瑶族傣族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '文山壮族苗族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '丘北县',
							'region_area' => '',
						],
						
						[
							'region_name' => '富宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '广南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '文山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '砚山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '西畴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '马关县',
							'region_area' => '',
						],
						
						[
							'region_name' => '麻栗坡县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '西双版纳傣族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '勐海县',
							'region_area' => '',
						],
						
						[
							'region_name' => '勐腊县',
							'region_area' => '',
						],
						
						[
							'region_name' => '景洪市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '大理白族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '云龙县',
							'region_area' => '',
						],
						
						[
							'region_name' => '剑川县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南涧彝族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '大理市',
							'region_area' => '',
						],
						
						[
							'region_name' => '宾川县',
							'region_area' => '',
						],
						
						[
							'region_name' => '巍山彝族回族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '弥渡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '洱源县',
							'region_area' => '',
						],
						
						[
							'region_name' => '漾濞彝族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '祥云县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鹤庆县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '德宏傣族景颇族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '梁河县',
							'region_area' => '',
						],
						
						[
							'region_name' => '潞西市',
							'region_area' => '',
						],
						
						[
							'region_name' => '瑞丽市',
							'region_area' => '',
						],
						
						[
							'region_name' => '盈江县',
							'region_area' => '',
						],
						
						[
							'region_name' => '陇川县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '怒江傈僳族自治州',
						'region_area' => '',
					],
					
					[
						'region_name' => '迪庆藏族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '德钦县',
							'region_area' => '',
						],
						
						[
							'region_name' => '维西傈僳族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '香格里拉县',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '西藏',
					'region_area' => '西南',
					'children' => 
					[
					
					[
						'region_name' => '拉萨市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '城关区',
							'region_area' => '',
						],
						
						[
							'region_name' => '堆龙德庆县',
							'region_area' => '',
						],
						
						[
							'region_name' => '墨竹工卡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '尼木县',
							'region_area' => '',
						],
						
						[
							'region_name' => '当雄县',
							'region_area' => '',
						],
						
						[
							'region_name' => '曲水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '林周县',
							'region_area' => '',
						],
						
						[
							'region_name' => '达孜县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '昌都地区',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '丁青县',
							'region_area' => '',
						],
						
						[
							'region_name' => '八宿县',
							'region_area' => '',
						],
						
						[
							'region_name' => '察雅县',
							'region_area' => '',
						],
						
						[
							'region_name' => '左贡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '昌都县',
							'region_area' => '',
						],
						
						[
							'region_name' => '江达县',
							'region_area' => '',
						],
						
						[
							'region_name' => '洛隆县',
							'region_area' => '',
						],
						
						[
							'region_name' => '类乌齐县',
							'region_area' => '',
						],
						
						[
							'region_name' => '芒康县',
							'region_area' => '',
						],
						
						[
							'region_name' => '贡觉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '边坝县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '山南地区',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '乃东县',
							'region_area' => '',
						],
						
						[
							'region_name' => '加查县',
							'region_area' => '',
						],
						
						[
							'region_name' => '扎囊县',
							'region_area' => '',
						],
						
						[
							'region_name' => '措美县',
							'region_area' => '',
						],
						
						[
							'region_name' => '曲松县',
							'region_area' => '',
						],
						
						[
							'region_name' => '桑日县',
							'region_area' => '',
						],
						
						[
							'region_name' => '洛扎县',
							'region_area' => '',
						],
						
						[
							'region_name' => '浪卡子县',
							'region_area' => '',
						],
						
						[
							'region_name' => '琼结县',
							'region_area' => '',
						],
						
						[
							'region_name' => '贡嘎县',
							'region_area' => '',
						],
						
						[
							'region_name' => '错那县',
							'region_area' => '',
						],
						
						[
							'region_name' => '隆子县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '日喀则地区',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '亚东县',
							'region_area' => '',
						],
						
						[
							'region_name' => '仁布县',
							'region_area' => '',
						],
						
						[
							'region_name' => '仲巴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南木林县',
							'region_area' => '',
						],
						
						[
							'region_name' => '吉隆县',
							'region_area' => '',
						],
						
						[
							'region_name' => '定日县',
							'region_area' => '',
						],
						
						[
							'region_name' => '定结县',
							'region_area' => '',
						],
						
						[
							'region_name' => '岗巴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '康马县',
							'region_area' => '',
						],
						
						[
							'region_name' => '拉孜县',
							'region_area' => '',
						],
						
						[
							'region_name' => '日喀则市',
							'region_area' => '',
						],
						
						[
							'region_name' => '昂仁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '江孜县',
							'region_area' => '',
						],
						
						[
							'region_name' => '白朗县',
							'region_area' => '',
						],
						
						[
							'region_name' => '聂拉木县',
							'region_area' => '',
						],
						
						[
							'region_name' => '萨嘎县',
							'region_area' => '',
						],
						
						[
							'region_name' => '萨迦县',
							'region_area' => '',
						],
						
						[
							'region_name' => '谢通门县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '那曲地区',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '嘉黎县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安多县',
							'region_area' => '',
						],
						
						[
							'region_name' => '尼玛县',
							'region_area' => '',
						],
						
						[
							'region_name' => '巴青县',
							'region_area' => '',
						],
						
						[
							'region_name' => '比如县',
							'region_area' => '',
						],
						
						[
							'region_name' => '班戈县',
							'region_area' => '',
						],
						
						[
							'region_name' => '申扎县',
							'region_area' => '',
						],
						
						[
							'region_name' => '索县',
							'region_area' => '',
						],
						
						[
							'region_name' => '聂荣县',
							'region_area' => '',
						],
						
						[
							'region_name' => '那曲县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '阿里地区',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '噶尔县',
							'region_area' => '',
						],
						
						[
							'region_name' => '措勤县',
							'region_area' => '',
						],
						
						[
							'region_name' => '改则县',
							'region_area' => '',
						],
						
						[
							'region_name' => '日土县',
							'region_area' => '',
						],
						
						[
							'region_name' => '普兰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '札达县',
							'region_area' => '',
						],
						
						[
							'region_name' => '革吉县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '林芝地区',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '墨脱县',
							'region_area' => '',
						],
						
						[
							'region_name' => '察隅县',
							'region_area' => '',
						],
						
						[
							'region_name' => '工布江达县',
							'region_area' => '',
						],
						
						[
							'region_name' => '朗县',
							'region_area' => '',
						],
						
						[
							'region_name' => '林芝县',
							'region_area' => '',
						],
						
						[
							'region_name' => '波密县',
							'region_area' => '',
						],
						
						[
							'region_name' => '米林县',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '陕西',
					'region_area' => '西北',
					'children' => 
					[
					
					[
						'region_name' => '西安市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '临潼区',
							'region_area' => '',
						],
						
						[
							'region_name' => '周至县',
							'region_area' => '',
						],
						
						[
							'region_name' => '户县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '未央区',
							'region_area' => '',
						],
						
						[
							'region_name' => '灞桥区',
							'region_area' => '',
						],
						
						[
							'region_name' => '碑林区',
							'region_area' => '',
						],
						
						[
							'region_name' => '莲湖区',
							'region_area' => '',
						],
						
						[
							'region_name' => '蓝田县',
							'region_area' => '',
						],
						
						[
							'region_name' => '长安区',
							'region_area' => '',
						],
						
						[
							'region_name' => '阎良区',
							'region_area' => '',
						],
						
						[
							'region_name' => '雁塔区',
							'region_area' => '',
						],
						
						[
							'region_name' => '高陵县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '铜川市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '印台区',
							'region_area' => '',
						],
						
						[
							'region_name' => '宜君县',
							'region_area' => '',
						],
						
						[
							'region_name' => '王益区',
							'region_area' => '',
						],
						
						[
							'region_name' => '耀州区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '宝鸡市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '凤县',
							'region_area' => '',
						],
						
						[
							'region_name' => '凤翔县',
							'region_area' => '',
						],
						
						[
							'region_name' => '千阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '太白县',
							'region_area' => '',
						],
						
						[
							'region_name' => '岐山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '扶风县',
							'region_area' => '',
						],
						
						[
							'region_name' => '渭滨区',
							'region_area' => '',
						],
						
						[
							'region_name' => '眉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '金台区',
							'region_area' => '',
						],
						
						[
							'region_name' => '陇县',
							'region_area' => '',
						],
						
						[
							'region_name' => '陈仓区',
							'region_area' => '',
						],
						
						[
							'region_name' => '麟游县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '咸阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '三原县',
							'region_area' => '',
						],
						
						[
							'region_name' => '干县',
							'region_area' => '',
						],
						
						[
							'region_name' => '兴平市',
							'region_area' => '',
						],
						
						[
							'region_name' => '彬县',
							'region_area' => '',
						],
						
						[
							'region_name' => '旬邑县',
							'region_area' => '',
						],
						
						[
							'region_name' => '杨陵区',
							'region_area' => '',
						],
						
						[
							'region_name' => '武功县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永寿县',
							'region_area' => '',
						],
						
						[
							'region_name' => '泾阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '淳化县',
							'region_area' => '',
						],
						
						[
							'region_name' => '渭城区',
							'region_area' => '',
						],
						
						[
							'region_name' => '礼泉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '秦都区',
							'region_area' => '',
						],
						
						[
							'region_name' => '长武县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '渭南市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '临渭区',
							'region_area' => '',
						],
						
						[
							'region_name' => '华县',
							'region_area' => '',
						],
						
						[
							'region_name' => '华阴市',
							'region_area' => '',
						],
						
						[
							'region_name' => '合阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '大荔县',
							'region_area' => '',
						],
						
						[
							'region_name' => '富平县',
							'region_area' => '',
						],
						
						[
							'region_name' => '潼关县',
							'region_area' => '',
						],
						
						[
							'region_name' => '澄城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '白水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '蒲城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '韩城市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '延安市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '吴起县',
							'region_area' => '',
						],
						
						[
							'region_name' => '子长县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安塞县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宜川县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宝塔区',
							'region_area' => '',
						],
						
						[
							'region_name' => '富县',
							'region_area' => '',
						],
						
						[
							'region_name' => '延川县',
							'region_area' => '',
						],
						
						[
							'region_name' => '延长县',
							'region_area' => '',
						],
						
						[
							'region_name' => '志丹县',
							'region_area' => '',
						],
						
						[
							'region_name' => '洛川县',
							'region_area' => '',
						],
						
						[
							'region_name' => '甘泉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '黄陵县',
							'region_area' => '',
						],
						
						[
							'region_name' => '黄龙县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '汉中市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '佛坪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '勉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '南郑县',
							'region_area' => '',
						],
						
						[
							'region_name' => '城固县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宁强县',
							'region_area' => '',
						],
						
						[
							'region_name' => '汉台区',
							'region_area' => '',
						],
						
						[
							'region_name' => '洋县',
							'region_area' => '',
						],
						
						[
							'region_name' => '留坝县',
							'region_area' => '',
						],
						
						[
							'region_name' => '略阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '西乡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '镇巴县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '榆林市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '佳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '吴堡县',
							'region_area' => '',
						],
						
						[
							'region_name' => '子洲县',
							'region_area' => '',
						],
						
						[
							'region_name' => '定边县',
							'region_area' => '',
						],
						
						[
							'region_name' => '府谷县',
							'region_area' => '',
						],
						
						[
							'region_name' => '榆林市榆阳区',
							'region_area' => '',
						],
						
						[
							'region_name' => '横山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '清涧县',
							'region_area' => '',
						],
						
						[
							'region_name' => '神木县',
							'region_area' => '',
						],
						
						[
							'region_name' => '米脂县',
							'region_area' => '',
						],
						
						[
							'region_name' => '绥德县',
							'region_area' => '',
						],
						
						[
							'region_name' => '靖边县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '安康市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '宁陕县',
							'region_area' => '',
						],
						
						[
							'region_name' => '岚皋县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平利县',
							'region_area' => '',
						],
						
						[
							'region_name' => '旬阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '汉滨区',
							'region_area' => '',
						],
						
						[
							'region_name' => '汉阴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '白河县',
							'region_area' => '',
						],
						
						[
							'region_name' => '石泉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '紫阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '镇坪县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '商洛市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '丹凤县',
							'region_area' => '',
						],
						
						[
							'region_name' => '商南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '商州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '山阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '柞水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '洛南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '镇安县',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '甘肃',
					'region_area' => '西北',
					'children' => 
					[
					
					[
						'region_name' => '兰州市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '七里河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '城关区',
							'region_area' => '',
						],
						
						[
							'region_name' => '安宁区',
							'region_area' => '',
						],
						
						[
							'region_name' => '榆中县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永登县',
							'region_area' => '',
						],
						
						[
							'region_name' => '皋兰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '红古区',
							'region_area' => '',
						],
						
						[
							'region_name' => '西固区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '嘉峪关市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '嘉峪关市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '金昌市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '永昌县',
							'region_area' => '',
						],
						
						[
							'region_name' => '金川区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '白银市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '会宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平川区',
							'region_area' => '',
						],
						
						[
							'region_name' => '景泰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '白银区',
							'region_area' => '',
						],
						
						[
							'region_name' => '靖远县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '天水市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '张家川回族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '武山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '清水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '甘谷县',
							'region_area' => '',
						],
						
						[
							'region_name' => '秦安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '秦州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '麦积区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '武威市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '凉州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '古浪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '天祝藏族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '民勤县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '张掖市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '临泽县',
							'region_area' => '',
						],
						
						[
							'region_name' => '山丹县',
							'region_area' => '',
						],
						
						[
							'region_name' => '民乐县',
							'region_area' => '',
						],
						
						[
							'region_name' => '甘州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '肃南裕固族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '高台县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '平凉市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '华亭县',
							'region_area' => '',
						],
						
						[
							'region_name' => '崆峒区',
							'region_area' => '',
						],
						
						[
							'region_name' => '崇信县',
							'region_area' => '',
						],
						
						[
							'region_name' => '庄浪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '泾川县',
							'region_area' => '',
						],
						
						[
							'region_name' => '灵台县',
							'region_area' => '',
						],
						
						[
							'region_name' => '静宁县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '酒泉市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '敦煌市',
							'region_area' => '',
						],
						
						[
							'region_name' => '玉门市',
							'region_area' => '',
						],
						
						[
							'region_name' => '瓜州县（原安西县）',
							'region_area' => '',
						],
						
						[
							'region_name' => '肃北蒙古族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '肃州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '金塔县',
							'region_area' => '',
						],
						
						[
							'region_name' => '阿克塞哈萨克族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '庆阳市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '华池县',
							'region_area' => '',
						],
						
						[
							'region_name' => '合水县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '庆城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '正宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '环县',
							'region_area' => '',
						],
						
						[
							'region_name' => '西峰区',
							'region_area' => '',
						],
						
						[
							'region_name' => '镇原县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '定西市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '临洮县',
							'region_area' => '',
						],
						
						[
							'region_name' => '安定区',
							'region_area' => '',
						],
						
						[
							'region_name' => '岷县',
							'region_area' => '',
						],
						
						[
							'region_name' => '渭源县',
							'region_area' => '',
						],
						
						[
							'region_name' => '漳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '通渭县',
							'region_area' => '',
						],
						
						[
							'region_name' => '陇西县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '陇南市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '两当县',
							'region_area' => '',
						],
						
						[
							'region_name' => '宕昌县',
							'region_area' => '',
						],
						
						[
							'region_name' => '康县',
							'region_area' => '',
						],
						
						[
							'region_name' => '徽县',
							'region_area' => '',
						],
						
						[
							'region_name' => '成县',
							'region_area' => '',
						],
						
						[
							'region_name' => '文县',
							'region_area' => '',
						],
						
						[
							'region_name' => '武都区',
							'region_area' => '',
						],
						
						[
							'region_name' => '礼县',
							'region_area' => '',
						],
						
						[
							'region_name' => '西和县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '临夏回族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东乡族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '临夏县',
							'region_area' => '',
						],
						
						[
							'region_name' => '临夏市',
							'region_area' => '',
						],
						
						[
							'region_name' => '和政县',
							'region_area' => '',
						],
						
						[
							'region_name' => '广河县',
							'region_area' => '',
						],
						
						[
							'region_name' => '康乐县',
							'region_area' => '',
						],
						
						[
							'region_name' => '永靖县',
							'region_area' => '',
						],
						
						[
							'region_name' => '积石山保安族东乡族撒拉族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '甘南藏族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '临潭县',
							'region_area' => '',
						],
						
						[
							'region_name' => '卓尼县',
							'region_area' => '',
						],
						
						[
							'region_name' => '合作市',
							'region_area' => '',
						],
						
						[
							'region_name' => '夏河县',
							'region_area' => '',
						],
						
						[
							'region_name' => '玛曲县',
							'region_area' => '',
						],
						
						[
							'region_name' => '碌曲县',
							'region_area' => '',
						],
						
						[
							'region_name' => '舟曲县',
							'region_area' => '',
						],
						
						[
							'region_name' => '迭部县',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '青海',
					'region_area' => '西北',
					'children' => 
					[
					
					[
						'region_name' => '西宁市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '城东区',
							'region_area' => '',
						],
						
						[
							'region_name' => '城中区',
							'region_area' => '',
						],
						
						[
							'region_name' => '城北区',
							'region_area' => '',
						],
						
						[
							'region_name' => '城西区',
							'region_area' => '',
						],
						
						[
							'region_name' => '大通回族土族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '湟中县',
							'region_area' => '',
						],
						
						[
							'region_name' => '湟源县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '海东地区',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '乐都县',
							'region_area' => '',
						],
						
						[
							'region_name' => '互助土族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '化隆回族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '平安县',
							'region_area' => '',
						],
						
						[
							'region_name' => '循化撒拉族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '民和回族土族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '海北藏族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '刚察县',
							'region_area' => '',
						],
						
						[
							'region_name' => '海晏县',
							'region_area' => '',
						],
						
						[
							'region_name' => '祁连县',
							'region_area' => '',
						],
						
						[
							'region_name' => '门源回族自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '黄南藏族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '同仁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '尖扎县',
							'region_area' => '',
						],
						
						[
							'region_name' => '河南蒙古族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '泽库县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '海南藏族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '共和县',
							'region_area' => '',
						],
						
						[
							'region_name' => '兴海县',
							'region_area' => '',
						],
						
						[
							'region_name' => '同德县',
							'region_area' => '',
						],
						
						[
							'region_name' => '贵南县',
							'region_area' => '',
						],
						
						[
							'region_name' => '贵德县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '果洛藏族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '久治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '玛多县',
							'region_area' => '',
						],
						
						[
							'region_name' => '玛沁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '班玛县',
							'region_area' => '',
						],
						
						[
							'region_name' => '甘德县',
							'region_area' => '',
						],
						
						[
							'region_name' => '达日县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '玉树藏族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '囊谦县',
							'region_area' => '',
						],
						
						[
							'region_name' => '曲麻莱县',
							'region_area' => '',
						],
						
						[
							'region_name' => '杂多县',
							'region_area' => '',
						],
						
						[
							'region_name' => '治多县',
							'region_area' => '',
						],
						
						[
							'region_name' => '玉树县',
							'region_area' => '',
						],
						
						[
							'region_name' => '称多县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '海西蒙古族藏族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '乌兰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '冷湖行委',
							'region_area' => '',
						],
						
						[
							'region_name' => '大柴旦行委',
							'region_area' => '',
						],
						
						[
							'region_name' => '天峻县',
							'region_area' => '',
						],
						
						[
							'region_name' => '德令哈市',
							'region_area' => '',
						],
						
						[
							'region_name' => '格尔木市',
							'region_area' => '',
						],
						
						[
							'region_name' => '茫崖行委',
							'region_area' => '',
						],
						
						[
							'region_name' => '都兰县',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '宁夏',
					'region_area' => '西北',
					'children' => 
					[
					
					[
						'region_name' => '银川市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '兴庆区',
							'region_area' => '',
						],
						
						[
							'region_name' => '永宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '灵武市',
							'region_area' => '',
						],
						
						[
							'region_name' => '西夏区',
							'region_area' => '',
						],
						
						[
							'region_name' => '贺兰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '金凤区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '石嘴山市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '大武口区',
							'region_area' => '',
						],
						
						[
							'region_name' => '平罗县',
							'region_area' => '',
						],
						
						[
							'region_name' => '惠农区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '吴忠市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '利通区',
							'region_area' => '',
						],
						
						[
							'region_name' => '同心县',
							'region_area' => '',
						],
						
						[
							'region_name' => '盐池县',
							'region_area' => '',
						],
						
						[
							'region_name' => '青铜峡市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '固原市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '原州区',
							'region_area' => '',
						],
						
						[
							'region_name' => '彭阳县',
							'region_area' => '',
						],
						
						[
							'region_name' => '泾源县',
							'region_area' => '',
						],
						
						[
							'region_name' => '西吉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '隆德县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '中卫市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '中宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '沙坡头区',
							'region_area' => '',
						],
						
						[
							'region_name' => '海原县',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '新疆',
					'region_area' => '西北',
					'children' => 
					[
					
					[
						'region_name' => '乌鲁木齐市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '东山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '乌鲁木齐县',
							'region_area' => '',
						],
						
						[
							'region_name' => '天山区',
							'region_area' => '',
						],
						
						[
							'region_name' => '头屯河区',
							'region_area' => '',
						],
						
						[
							'region_name' => '新市区',
							'region_area' => '',
						],
						
						[
							'region_name' => '水磨沟区',
							'region_area' => '',
						],
						
						[
							'region_name' => '沙依巴克区',
							'region_area' => '',
						],
						
						[
							'region_name' => '达坂城区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '克拉玛依市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '乌尔禾区',
							'region_area' => '',
						],
						
						[
							'region_name' => '克拉玛依区',
							'region_area' => '',
						],
						
						[
							'region_name' => '独山子区',
							'region_area' => '',
						],
						
						[
							'region_name' => '白碱滩区',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '吐鲁番地区',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '吐鲁番市',
							'region_area' => '',
						],
						
						[
							'region_name' => '托克逊县',
							'region_area' => '',
						],
						
						[
							'region_name' => '鄯善县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '哈密地区',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '伊吾县',
							'region_area' => '',
						],
						
						[
							'region_name' => '哈密市',
							'region_area' => '',
						],
						
						[
							'region_name' => '巴里坤哈萨克自治县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '昌吉回族自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '吉木萨尔县',
							'region_area' => '',
						],
						
						[
							'region_name' => '呼图壁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '奇台县',
							'region_area' => '',
						],
						
						[
							'region_name' => '昌吉市',
							'region_area' => '',
						],
						
						[
							'region_name' => '木垒哈萨克自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '玛纳斯县',
							'region_area' => '',
						],
						
						[
							'region_name' => '米泉市',
							'region_area' => '',
						],
						
						[
							'region_name' => '阜康市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '博尔塔拉蒙古自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '博乐市',
							'region_area' => '',
						],
						
						[
							'region_name' => '温泉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '精河县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '巴音郭楞蒙古自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '博湖县',
							'region_area' => '',
						],
						
						[
							'region_name' => '和硕县',
							'region_area' => '',
						],
						
						[
							'region_name' => '和静县',
							'region_area' => '',
						],
						
						[
							'region_name' => '尉犁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '库尔勒市',
							'region_area' => '',
						],
						
						[
							'region_name' => '焉耆回族自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '若羌县',
							'region_area' => '',
						],
						
						[
							'region_name' => '轮台县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '阿克苏地区',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '乌什县',
							'region_area' => '',
						],
						
						[
							'region_name' => '库车县',
							'region_area' => '',
						],
						
						[
							'region_name' => '拜城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新和县',
							'region_area' => '',
						],
						
						[
							'region_name' => '柯坪县',
							'region_area' => '',
						],
						
						[
							'region_name' => '沙雅县',
							'region_area' => '',
						],
						
						[
							'region_name' => '温宿县',
							'region_area' => '',
						],
						
						[
							'region_name' => '阿克苏市',
							'region_area' => '',
						],
						
						[
							'region_name' => '阿瓦提县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '克孜勒苏柯尔克孜自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '乌恰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '阿克陶县',
							'region_area' => '',
						],
						
						[
							'region_name' => '阿合奇县',
							'region_area' => '',
						],
						
						[
							'region_name' => '阿图什市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '喀什地区',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '伽师县',
							'region_area' => '',
						],
						
						[
							'region_name' => '叶城县',
							'region_area' => '',
						],
						
						[
							'region_name' => '喀什市',
							'region_area' => '',
						],
						
						[
							'region_name' => '塔什库尔干塔吉克自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '岳普湖县',
							'region_area' => '',
						],
						
						[
							'region_name' => '巴楚县',
							'region_area' => '',
						],
						
						[
							'region_name' => '泽普县',
							'region_area' => '',
						],
						
						[
							'region_name' => '疏勒县',
							'region_area' => '',
						],
						
						[
							'region_name' => '疏附县',
							'region_area' => '',
						],
						
						[
							'region_name' => '英吉沙县',
							'region_area' => '',
						],
						
						[
							'region_name' => '莎车县',
							'region_area' => '',
						],
						
						[
							'region_name' => '麦盖提县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '和田地区',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '于田县',
							'region_area' => '',
						],
						
						[
							'region_name' => '和田县',
							'region_area' => '',
						],
						
						[
							'region_name' => '和田市',
							'region_area' => '',
						],
						
						[
							'region_name' => '墨玉县',
							'region_area' => '',
						],
						
						[
							'region_name' => '民丰县',
							'region_area' => '',
						],
						
						[
							'region_name' => '洛浦县',
							'region_area' => '',
						],
						
						[
							'region_name' => '皮山县',
							'region_area' => '',
						],
						
						[
							'region_name' => '策勒县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '伊犁哈萨克自治州',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '伊宁县',
							'region_area' => '',
						],
						
						[
							'region_name' => '伊宁市',
							'region_area' => '',
						],
						
						[
							'region_name' => '奎屯市',
							'region_area' => '',
						],
						
						[
							'region_name' => '察布查尔锡伯自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '尼勒克县',
							'region_area' => '',
						],
						
						[
							'region_name' => '巩留县',
							'region_area' => '',
						],
						
						[
							'region_name' => '新源县',
							'region_area' => '',
						],
						
						[
							'region_name' => '昭苏县',
							'region_area' => '',
						],
						
						[
							'region_name' => '特克斯县',
							'region_area' => '',
						],
						
						[
							'region_name' => '霍城县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '塔城地区',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '乌苏市',
							'region_area' => '',
						],
						
						[
							'region_name' => '和布克赛尔蒙古自治县',
							'region_area' => '',
						],
						
						[
							'region_name' => '塔城市',
							'region_area' => '',
						],
						
						[
							'region_name' => '托里县',
							'region_area' => '',
						],
						
						[
							'region_name' => '沙湾县',
							'region_area' => '',
						],
						
						[
							'region_name' => '裕民县',
							'region_area' => '',
						],
						
						[
							'region_name' => '额敏县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '阿勒泰地区',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '吉木乃县',
							'region_area' => '',
						],
						
						[
							'region_name' => '哈巴河县',
							'region_area' => '',
						],
						
						[
							'region_name' => '富蕴县',
							'region_area' => '',
						],
						
						[
							'region_name' => '布尔津县',
							'region_area' => '',
						],
						
						[
							'region_name' => '福海县',
							'region_area' => '',
						],
						
						[
							'region_name' => '阿勒泰市',
							'region_area' => '',
						],
						
						[
							'region_name' => '青河县',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '石河子市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '石河子市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '阿拉尔市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '阿拉尔市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '图木舒克市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '图木舒克市',
							'region_area' => '',
						],
						],
					],
					
					[
						'region_name' => '五家渠市',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '五家渠市',
							'region_area' => '',
						],
						],
					],
					],
				],
				
				[
					'region_name' => '台湾',
					'region_area' => '港澳台',
					'children' => 
					[
					
					[
						'region_name' => '台北市',
						'region_area' => '',
					],
					
					[
						'region_name' => '高雄市',
						'region_area' => '',
					],
					
					[
						'region_name' => '基隆市',
						'region_area' => '',
					],
					
					[
						'region_name' => '台中市',
						'region_area' => '',
					],
					
					[
						'region_name' => '台南市',
						'region_area' => '',
					],
					
					[
						'region_name' => '新竹市',
						'region_area' => '',
					],
					
					[
						'region_name' => '嘉义市',
						'region_area' => '',
					],
					
					[
						'region_name' => '台北县',
						'region_area' => '',
					],
					
					[
						'region_name' => '宜兰县',
						'region_area' => '',
					],
					
					[
						'region_name' => '桃园县',
						'region_area' => '',
					],
					
					[
						'region_name' => '新竹县',
						'region_area' => '',
					],
					
					[
						'region_name' => '苗栗县',
						'region_area' => '',
					],
					
					[
						'region_name' => '台中县',
						'region_area' => '',
					],
					
					[
						'region_name' => '彰化县',
						'region_area' => '',
					],
					
					[
						'region_name' => '南投县',
						'region_area' => '',
					],
					
					[
						'region_name' => '云林县',
						'region_area' => '',
					],
					
					[
						'region_name' => '嘉义县',
						'region_area' => '',
					],
					
					[
						'region_name' => '台南县',
						'region_area' => '',
					],
					
					[
						'region_name' => '高雄县',
						'region_area' => '',
					],
					
					[
						'region_name' => '屏东县',
						'region_area' => '',
					],
					
					[
						'region_name' => '澎湖县',
						'region_area' => '',
					],
					
					[
						'region_name' => '台东县',
						'region_area' => '',
					],
					
					[
						'region_name' => '花莲县',
						'region_area' => '',
					],
					],
				],
				
				[
					'region_name' => '香港',
					'region_area' => '港澳台',
					'children' => 
					[
					
					[
						'region_name' => '中西区',
						'region_area' => '',
					],
					
					[
						'region_name' => '东区',
						'region_area' => '',
					],
					
					[
						'region_name' => '九龙城区',
						'region_area' => '',
					],
					
					[
						'region_name' => '观塘区',
						'region_area' => '',
					],
					
					[
						'region_name' => '南区',
						'region_area' => '',
					],
					
					[
						'region_name' => '深水埗区',
						'region_area' => '',
					],
					
					[
						'region_name' => '黄大仙区',
						'region_area' => '',
					],
					
					[
						'region_name' => '湾仔区',
						'region_area' => '',
					],
					
					[
						'region_name' => '油尖旺区',
						'region_area' => '',
					],
					
					[
						'region_name' => '离岛区',
						'region_area' => '',
					],
					
					[
						'region_name' => '葵青区',
						'region_area' => '',
					],
					
					[
						'region_name' => '北区',
						'region_area' => '',
					],
					
					[
						'region_name' => '西贡区',
						'region_area' => '',
					],
					
					[
						'region_name' => '沙田区',
						'region_area' => '',
					],
					
					[
						'region_name' => '屯门区',
						'region_area' => '',
					],
					
					[
						'region_name' => '大埔区',
						'region_area' => '',
					],
					
					[
						'region_name' => '荃湾区',
						'region_area' => '',
					],
					
					[
						'region_name' => '元朗区',
						'region_area' => '',
					],
					],
				],
				
				[
					'region_name' => '澳门',
					'region_area' => '港澳台',
					'children' => 
					[
					
					[
						'region_name' => '澳门特别行政区',
						'region_area' => '',
					],
					],
				],
				[
					'region_name' => '海外',
					'region_area' => '海外',
					'children' => 
					[
					
					[
						'region_name' => '海外',
						'region_area' => '',
						'children' => 
						[
						
						[
							'region_name' => '美国',
							'region_area' => '',
						],
						
						[
							'region_name' => '加拿大',
							'region_area' => '',
						],
						
						[
							'region_name' => '澳大利亚',
							'region_area' => '',
						],
						
						[
							'region_name' => '新西兰',
							'region_area' => '',
						],
						
						[
							'region_name' => '英国',
							'region_area' => '',
						],
						
						[
							'region_name' => '法国',
							'region_area' => '',
						],
						
						[
							'region_name' => '德国',
							'region_area' => '',
						],
						
						[
							'region_name' => '捷克',
							'region_area' => '',
						],
						
						[
							'region_name' => '荷兰',
							'region_area' => '',
						],
						
						[
							'region_name' => '瑞士',
							'region_area' => '',
						],
						
						[
							'region_name' => '希腊',
							'region_area' => '',
						],
						
						[
							'region_name' => '挪威',
							'region_area' => '',
						],
						
						[
							'region_name' => '瑞典',
							'region_area' => '',
						],
						
						[
							'region_name' => '丹麦',
							'region_area' => '',
						],
						
						[
							'region_name' => '芬兰',
							'region_area' => '',
						],
						
						[
							'region_name' => '爱尔兰',
							'region_area' => '',
						],
						
						[
							'region_name' => '奥地利',
							'region_area' => '',
						],
						
						[
							'region_name' => '意大利',
							'region_area' => '',
						],
						
						[
							'region_name' => '乌克兰',
							'region_area' => '',
						],
						
						[
							'region_name' => '俄罗斯',
							'region_area' => '',
						],
						
						[
							'region_name' => '西班牙',
							'region_area' => '',
						],
						
						[
							'region_name' => '韩国',
							'region_area' => '',
						],
						
						[
							'region_name' => '新加坡',
							'region_area' => '',
						],
						
						[
							'region_name' => '马来西亚',
							'region_area' => '',
						],
						
						[
							'region_name' => '印度',
							'region_area' => '',
						],
						
						[
							'region_name' => '泰国',
							'region_area' => '',
						],
						
						[
							'region_name' => '日本',
							'region_area' => '',
						],
						
						[
							'region_name' => '巴西',
							'region_area' => '',
						],
						
						[
							'region_name' => '阿根廷',
							'region_area' => '',
						],
						
						[
							'region_name' => '南非',
							'region_area' => '',
						],
						
						[
							'region_name' => '埃及',
							'region_area' => '',
						],
						],
					],
					],
				],
			];
		
		Region::truncate();

		$this->command->info('开始填充地区');

		$this->insertTree($regionTreeArray, 0, 1);
            
		$this->command->info('✅填充地区完成');

    }

	#[DocParams(note:'递归插入地区树',params:['nodes'=>'当前层级节点数组','parentId'=>'父级ID','deep'=>'当前深度'])]
    protected function insertTree(array $nodes, int $parentId, int $deep)
    {
        foreach ($nodes as $node) {
            // 插入当前节点
            $item = Region::create([
                'region_name' => $node['region_name'],
                'region_area' => $node['region_area'],
                'latitude' => '',
                'longitude' => '',
				'parent_id' => $parentId,
                'deep' => $deep,
				'sort' => 100,
                'created_time' => time(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // 递归插入子节点，父ID为刚插入的主键
            if (!empty($node['children'])) {
                $this->insertTree($node['children'], $item->id, $deep + 1);
            }
        }
    }
}


