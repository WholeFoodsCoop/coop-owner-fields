<?php
/**
 * @package Co-op Owner Fields
 */
/*
Plugin Name: Co-op Owner Fields
Plugin URI: https://github.com/CORE-POS
Description: Co-op Owner Fields provides additional profile fields to designate users as co-op owners
Version: 1.0
Author: Andy Theuninck
Author URI: https://github.com/gohanman/
License: Apache2
Text Domain: coop-owner-fields
*/

function coopOwnerFieldsDisplay()
{
    $userID = get_current_user_id();
    $label = translate('Co-op Owner');
    $value = get_user_meta($userID, 'cof_owner', true);
    $opts = array(0 => translate('No'), 1 => translate('Yes'));
    $ownerID = get_user_meta($userID, 'cof_cardno', true);
    $select = '';
    foreach ($opts as $id => $o) {
        $select .= sprintf('<option %s value="%d">%s</option>',
            ($id == $value ? 'selected' : ''), $id, $o);
    }
?>
<table class="form-table">
<tr>
    <th><label for="cof_owner"><?php echo $label; ?></label></th>
    <td>
        <select name="cof_owner" id="cof_owner">
        <?php echo $select; ?>
        </select>
    </td>
    <th><label for="cof_cardno"><?php _e('Owner #'); ?></label></th>
    <td><input type="number" name="cof_cardno" id="cof_cardno"
        value="<?php echo $ownerID; ?>" /></td>
</tr>
</table>
<?php
}

/**
  cof_owner is just a boolean flag
  but if 
    1) an "owner" role exists and
    2) the user is not an admin or editor
  the current user's role is changed to
  "owner". 
*/
function coopOwnerFieldsSave($userID)
{
    if (current_user_can('edit_user', $userID)) {
        $value = (int)$_POST['cof_owner'];
        if ($value !== 1) {
            $value = 0;
        }
        $current = get_user_meta($userID, 'cof_owner', true);
        if ($current === false || $current === '') {
            add_user_meta($userID, 'cof_owner', $value, true);
        } else {
            update_user_meta($userID, 'cof_owner', $value);
        }

        $cardno = (int)$_POST['cof_cardno'];
        $current = get_user_meta($userID, 'cof_cardno', true);
        if ($current === false || $current === '') {
            add_user_meta($userID, 'cof_cardno', $cardno, true);
        } else {
            update_user_meta($userID, 'cof_cardn', $cardno);
        }

        $role = get_role('owner');
        if ($role !== 'null' && !current_user_can('administrator') && !current_user_can('editor')) {
            $user = wp_get_current_user();
            $user->set_role('owner');
        }
    }
}

add_action('woocommerce_edit_account_form', 'coopOwnerFieldsDisplay');
add_action('woocommerce_save_account_details', 'coopOwnerFieldsSave');

