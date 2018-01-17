<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<?php
switch ($rbdigitalLink['status']) {
    case 'OK': {
            $browse_link = $rbdigitalLink['launcher_url'] . '?user_id=' . $rbdigitalLink['user_id'] . '&library_id=' . $rbdigitalLink['library_id'] . '&library_token=' . $rbdigitalLink['library_token'] . '&api_url=' . $rbdigitalLink['api_url'];
            ?>
			<p><?php echo $rbdigitalDescription; ?></p>
            <p class="rt-center"><a id="view-collection" class="readon2 rt-big-button" href="<?php echo $browse_link ?>" target="_blank"><?php echo JText::_('MOD_ZINIOLOADER_TEMPLATE_LANUCH'); ?></a></p>
            
                <?php
            break;
        }
    case 'DEBUG': {
            ?>
            <ul class='unstyled'>
                <li><b>Mode</b>:<?php echo $rbdigitalLink['status'] ?></li>
                <li><b>API URL</b>:<?php echo $rbdigitalLink['api_url'] ?></li>
                <li><b>User ID</b>:<?php echo $rbdigitalLink['user_id'] ?></li>
                <li><b>Library ID</b>:<?php echo $rbdigitalLink['library_id'] ?></li>
                <li><b>Library Token</b>:<?php echo $rbdigitalLink['library_token'] ?></li>
                <li><b>HTTP Code</b>:<?php echo $rbdigitalLink['http_code'] ?></li>
                <li><b>Response Status</b>:<?php echo $rbdigitalLink['response_status'] ?></li>
            </ul>
            <?php
            break;
        }    
    default: {
            ?>
            <p><?php echo JText::_('MOD_ZINIOLOADER_ERROR_MESSAGE'); ?></p>
            <?php
        }
}
?>
