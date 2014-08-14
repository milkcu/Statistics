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
include 'common.php';
include 'header.php';
include 'menu.php';
?>
<div class="main">
    <div class="body body-950">
		<?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-24 start-01 typecho-list">
                <div class="typecho-list-operate">
                    <?php
						$config  = Typecho_Widget::widget('Widget_Options')->plugin('Statistics');
						$pagecount = $config->pagecount;
						$isdrop = $config->droptable;
						if ($pagecount == null || $isdrop == null)
						{
							throw new Typecho_Plugin_Exception('请先设置插件！');
						}
						$db = Typecho_Db::get();
						$prefix = $db->getPrefix();
						$p = 1;
						$rtype = '';
						$oldtype = '';
						if (isset($_POST['rpage'])) {
							$p = $_POST['rpage'];
						}
						if (isset($_POST['do'])) {
							$do = $_POST['do'];
						}
						if (isset($_POST['oldtype'])) {
							$oldtype = $_POST['oldtype'];
						}
						$logs = $db->fetchAll($db->select()->from($prefix.'visit_logs')->order($prefix.'visit_logs.vlid', Typecho_Db::SORT_DESC)->page($p, $pagecount));
						$rows = count($db->fetchAll($db->select('vlid')->from($prefix.'visit_logs')));
						$co = $rows % $pagecount;
						$pageno = floor($rows / $pagecount);
						if ($co !== 0) {
							$pageno += 1;
						}
                    ?>
                <form method="post" action="<?php $options->adminUrl('extending.php?panel=Statistics%2FVisitLogs.php'); ?>">
                    <p class="operate">操作:</p>
                    <p class="search">
	                    <select name="rpage">
	                        <?php for ($i = 1; $i <= $pageno; $i++): ?>
	                    	<option value="<?php echo $i ?>" <?php if ($i == $p): ?>selected="selected"<?php endif; ?>>第<?php echo $i ?>页</option>
	                        <?php endfor; ?>
	                    </select>
	                    <select name="rtype">
	                    	<option value="">所有蜘蛛</option>
	                    </select>
	                    <button type="submit">查看</button>
                    </p>
                <input type="hidden" name="do" value="select" />
                <input type="hidden" name="oldtype" value="<?php echo $rtype; ?>" />
                </form>
                </div>
            
                <form method="post" action="<?php $options->adminUrl('extending.php?panel=Statistics%2FLogs.php'); ?>">
                <table class="typecho-list-table">
                    <colgroup>
                        <col width="210"/>
                        <col width="210"/>
                        <col width="240"/>
                        <col width="110"/>
                        <col width="120"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th>受访地址</th>
                            <th>来源地址</th>
                            <th>用户代理</th>
                            <th>IP地址<a style="padding-left:12px;" href="javascript:void(0);" onclick="showIpLocation();">查询位置</a></th>
                            <th class="typecho-radius-topright">日期</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($logs)): ?>
                    <?php foreach ($logs as $log): ?>
						<tr class="even" id="post-5">
                            <td><a href="<?php echo $log['vurl']; ?>"><?php echo $log['vurl']; ?></a></td>
                            <td><a href="<?php echo $log['furl']; ?>"><?php echo $log['furl']; ?></a></td>
                            <td><?php echo $log['vagent']; ?></td>
                            <td><div class="robotx_ip"><?php echo $log['vip']; ?></div><div class="robotx_location"></div></td>
                            <td><?php echo date('Y-m-d H:i:s',$log['vtime']); ?></td>
                        </tr>
					<?php endforeach; ?>
                    <?php else: ?>
                    <tr class="even">
                        <td colspan="8"><h6 class="typecho-list-table-title"><?php _e('当前无访问记录'); ?></h6></td>
                    </tr>
                    <?php endif; ?>
					</tbody>
                </table>
                <input type="hidden" name="do" value="delete" />
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="http://lib.sinaapp.com/js/jquery/1.4.1/jquery.min.js"></script>
<script type="text/javascript">
/*解决jquery库Mootools库之间的冲突*/
jQuery.noConflict();//释放jquery中$定义，并直接使用jQuery代替平时的$
function showIpLocation(){	
		jQuery(".robotx_location").text("正在查询...");		
		jQuery(".robotx_ip").each(function(){
			var myd = jQuery(this);
		  jQuery.getScript("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=" + myd.text(),function(){ 
		  	var ipadd = "没有找到匹配的 IP 地址信息";
			  if (remote_ip_info.ret == '1'){
			 			ipadd = remote_ip_info.country + " " 
					  + remote_ip_info.province + " " 
					  + remote_ip_info.district + " " 
					  + remote_ip_info.desc + " " 
					  + remote_ip_info.isp;
					  myd.next().text(ipadd).css("color","#BD6800");
				}else{
					myd.next().text(ipadd).css("color","#f00");
				}				
			});
	});
}
</script>

<?php
include 'copyright.php';
include 'common-js.php';
?>
<?php include 'footer.php'; ?>