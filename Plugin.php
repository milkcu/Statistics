<?php
/**
 * 博客信息统计软件
 * 
 * @package Statistics
 * @author  MilkCu
 * @version 1.0.0
 * @update: 2014.08.08
 * @link http://www.milkcu.com
 */
class Statistics_Plugin implements Typecho_Plugin_Interface
{
    public static function activate()
    {
		$meg = Statistics_Plugin::install();
		Helper::addPanel(1, 'Statistics/VisitLogs.php', '访问记录', '查看访问记录', 'administrator');
        Typecho_Plugin::factory('Widget_Archive')->header = array('Statistics_Plugin', 'visit');
		return _t($meg.'。请进行<a href="options-plugin.php?config=Statistics">初始化设置</a>');
    }
    public static function deactivate()
	{
		$config  = Typecho_Widget::widget('Widget_Options')->plugin('Statistics');
		$isdrop = $config->droptable;
		if ($isdrop == 0)
		{
			$db = Typecho_Db::get();
			$prefix = $db->getPrefix();
			$db->query("DROP TABLE `".$prefix."visit_logs`", Typecho_Db::WRITE);
		}
		Helper::removePanel(1, 'Statistics/VisitLogs.php');
	}
    public static function config(Typecho_Widget_Helper_Form $form)
	{
		$pagecount = new Typecho_Widget_Helper_Form_Element_Text(
          'pagecount', NULL, '',
          '分页数量', '每页显示的记录数量');
		$dbool = array (
			'0' => '删除',
			'1' => '不删除'
			);
		$droptable = new Typecho_Widget_Helper_Form_Element_Radio(
			'droptable', $dbool, '',
          	'删除数据表:', '请选择是否在禁用插件时，删除日志数据表');
		$form->addInput($pagecount);
		$form->addInput($droptable);
	}
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
	{
	}
	public static function install()
	{
		$installDb = Typecho_Db::get();
		$type = explode('_', $installDb->getAdapterName());
		$type = array_pop($type);
		$prefix = $installDb->getPrefix();
		$scripts = file_get_contents('usr/plugins/Statistics/Mysql.sql');
		$scripts = str_replace('typecho_', $prefix, $scripts);
		$scripts = str_replace('%charset%', 'utf8', $scripts);
		$scripts = explode(';', $scripts);
		try {
			foreach ($scripts as $script) {
				$script = trim($script);
				if ($script) {
					$installDb->query($script, Typecho_Db::WRITE);
				}
			}
			return '成功创建数据表，插件启用成功';
		} catch (Typecho_Db_Exception $e) {
			$code = $e->getCode();
			if(('Mysql' == $type && 1050 == $code)) {
					$script = 'SELECT `lid`, `bot`, `url`, `ip`, `ltime` from `' . $prefix . 'visit_logs`';
					$installDb->query($script, Typecho_Db::READ);
					return '数据表已存在，插件启用成功';	
			} else {
				throw new Typecho_Plugin_Exception('数据表建立失败，插件启用失败。错误号：'.$code);
			}
		}
	}
    public static function visit($rule = NULL)
    {
		@ $useragent = $_SERVER['HTTP_USER_AGENT'];
		@ $from = $_SERVER["HTTP_REFERER"];
		$request = new Typecho_Request;
		$ip = $request->getIp();
		$url = $_SERVER['REQUEST_URI'];
		if ($ip == NULL){
			$ip = 'UnKnow';
		}
		$options = Typecho_Widget::widget('Widget_Options');
		$timeStamp = $options->gmtTime;
		$offset = $options->timezone - $options->serverTimezone;
		$gtime = $timeStamp + $offset;
		$db = Typecho_Db::get();
		$rows = array (
			'vagent' => $useragent,
			'vurl' => $url,
			'furl' => $from,
			'vip' => $ip,
			'vtime' => $gtime,
			);
		$db->query($db->insert('table.visit_logs')->rows($rows));
    }
}
