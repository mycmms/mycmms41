<html>
<head>
<?PHP
/** 
* @author  Werner Huysmans 
* @access  public
* @package mycmms40
* @subpackage framework
* @filesource
*/
?>
<script>
opener.document.<?PHP echo $treeform.".".$ID.".value='".$_REQUEST['ID']."'"; ?>;
opener.document.<?PHP echo $treeform.".".$ID_DESC.".value='".$_REQUEST['ID_DESC']."'"; ?>;
window.close();
</script>
</head>
</html> 