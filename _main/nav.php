<?PHP
/**  nav.php: composes the menu with the elements in table sys_navigation.
* @author  Werner Huysmans <werner.huysmans@skynet.be>
* @access  public
* @version 4.0 
* @package mycmms40
* @subpackage framework
* @filesource
*/
$nosecurity_check=true;
require("../includes/config_mycmms.inc.php");
$DB=DBC::get();
// $nav=$_SESSION['nav'];  
if(empty($_SESSION['user'])) {    
    exit; 
}
$_SESSION['from']=0;
/** 
 * @global Uses the $_SESSION['system'] variable 
 * @param of the query (table mycmms.sys_queries)
 * @return returns HyperLink
 */
function url($name) {   
    global $DB;     // =DBC::get();
    global $rootdirs;
/*  Retrieving which system this is, this functions is obsolete now...
*/
    switch ($_SESSION['system']) {
    case "td":
    case "production":
    case "oee":
	case "home":
        $list_php="list.php";
        break;
    default:
        $list_php="list.php";
        break;    
    }
    if ($_SESSION['profile']==1) {   
/** Show all options to profile development, so be careful...
* 
*/
        $sql="SELECT * FROM sys_queries WHERE name LIKE '$name'";
    } else {
/** Security level 1: showing only those lists that respond to the profile of the user.
*         
*/
        $sql="SELECT * FROM sys_queries WHERE name LIKE '$name' AND (profile & {$_SESSION['profile']}) <> 0";
    }
    $result=$DB->query($sql);
    $numrecs=DBC::numrows($sql);
    if ($numrecs==0) {
        return FALSE;
        exit; 
    }
/*  Action depends on Prefix
*   P_  Parameter list
*   A_  Action   
*   S_  Search
*   W_  Window
*/
    $table_bp = $result->fetch(PDO::FETCH_OBJ);
    $prefix = substr($table_bp->name,0,2);
//    mb_internal_encoding("UTF-8");
//    $caption=iconv("UTF-8","ISO8859-1",_($table_bp->caption));
//  $title=iconv("UTF-8","ISO8859-1",_($table_bp->title));
    $caption=_($table_bp->caption);
    $title=_($table_bp->title);
    switch ($prefix) {
    case "P_":
/** Originally it was intended to open a special window...
*         if (strlen($table_bp->window) > 2) {
*           $url = "<a href=\"{$table_bp->window}?query_name=$name\" target=\"maintmain\">"._($table_bp->caption)."</a><BR>";
*       }
*/
        $url = "<a href=\"param.php?query_name=$name&amp;system={$_SESSION['system']}\" target=\"maintmain\">"._($table_bp->caption)."</a><br />";
        break;
    case "A_":
        $top_title=_($table_bp->title);
        $url="<a href='../actions/_redirect.php?action={$table_bp->mysql}&amp;title={$top_title}' target='maintmain'><b>$caption</b></a><br />";   
        break;
    case "S_":
        $html = "<form action='search_record.php' target='maintmain' method='post'>
                    <input type='hidden' name='QUERY_NAME' value='%s' />
                    %s <input type='text' name='ID' size='15' style='vertical-align:text-bottom' />
                    </form>";
        $url = sprintf($html,$table_bp->mysql,$caption);
        break;    
    case "W_":
        $_SESSION['Ident_1']="new";
        $_SESSION['Ident_2']="";
        $javascript="<a href=\"javascript://\" onclick=\"openwindow2('".$_SESSION['Ident_1']."','".$_SESSION['Ident_2']."','%s')\">%s</a><br />";
        eval('$table_bp->mysql="'.$table_bp->mysql.'";');
        $url=sprintf($javascript,$table_bp->mysql,$caption);
        break;
    case "M_":
        eval('$table_bp->mysql = "'.$table_bp->mysql.'" ;');
        $prefix="<a href=\"list_smarty.php?query_name=$name\" target=\"maintmain\">"; //All links begin the same
        unset($_SESSION['order_by']);
        $num = count_records($table_bp->mysql);
        if($num >0)
        {   $url=$prefix.$caption." - ".$num."</a><br>";
        } else {
            $url="<span class=\"no_link\">$caption - ".count_records("$table_bp->mysql") . "</span><br />";
        }
        break;                          
    default:
        eval('$table_bp->mysql = "'.$table_bp->mysql.'" ;');
        $prefix="<a href=\"$list_php?query_name=$name\" target=\"maintmain\">"; //All links begin the same
        unset($_SESSION['order_by']);
        $num=count_records($table_bp->mysql);
        if($num >0)
        {   $url=$prefix.$caption." - ".$num."</a><br />";
        } else {
            $url="<span class=\"no_link\">$caption - ".count_records("$table_bp->mysql")."</span><br />";
        }
        break;
    }
    return $url;
}
function count_records($sql) 
{    
    $numrecs=DBC::numrows($sql);
    if($numrecs>0) {    
        $num=$numrecs; 
    } else {    
        $num = "0"; 
    }
    return $num;
}

require("HTML/FC_Menu.php");     //manu class file
$menu = new Menu();         //create a new menu object
$result=$DB->query("SELECT CAT,LINK FROM sys_navigation WHERE NAV='{$_SESSION['nav']}' ORDER BY MENUORDER");
if ($result) {
    foreach ($result->fetchAll(PDO::FETCH_OBJ) as $row) {
        $menu->addLink($row->LINK,_($row->CAT));
    }
}

require("setup.php");
$tpl=new smarty_mycmms();
$tpl->assign('stylesheet',STYLE_PATH."/".CSS_NAVIGATION);
$tpl->assign('menu',$menu->toHtml());
$tpl->display("framework_nav.tpl");
?>
