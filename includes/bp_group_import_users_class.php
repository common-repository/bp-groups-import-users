<?php
/**
 * Initialise BP GROUPS IMPORT USERS
 *
 * @class       bp_group_import_users_class
 * @author      H.K.Latiyan (Vibethemes)
 * @category    Admin
 * @package     bp_group_import_users/includes
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class bp_group_import_users_class{

	public static $instance;
	public static function instance_bp_group_import_users_class(){
	    if ( is_null( self::$instance ) )
	        self::$instance = new bp_group_import_users_class();
	    return self::$instance;
	}

	private function __construct(){
		
		add_action('bp_after_group_manage_members_admin',array($this,'add_bulk_members_to_group'));

	} // END public function __construct

	public function activate(){

	}

	public function deactivate(){
		
	}

	function add_bulk_members_to_group(){

		if(!is_user_logged_in())
			return;

		$user_id = get_current_user_id();
		$admins = bp_group_admin_ids();

		if(is_numeric($admins)){
			$admins = array($admins);
		}else{
			$admins = explode(',',$admins);
		}

		$flag = 1;
		$flag = apply_filters('bp_check_admins_and_upload_users',$flag,$admins);
		if(!$flag)
			return;

		if(!in_array($user_id,$admins))
			return;

		//Form for bulk add users to group
		?>
		<div class="add_bulk_members_in_group">
		<h3 class="heading"><span><?php _e('Add Bulk Members','bp-giu'); ?></span><a href="<?php echo plugins_url('../assets/sample.csv', __FILE__); ?>"><?php _e('Download Sample CSV File','bp-giu'); ?></a></h3>

		<form method="post" action="/">
			<input type="file" name="users_csv" id="users_csv_file">
			<div class="checkbox">
				<input type="checkbox" name="create_user" id="create_user" value="1" style="float:left;margin-right:10px;" />
				<label for="create_user"><?php _e('Create user if not exists','bp-giu'); ?></label>
			</div>
			<input type="submit" value="<?php _e('Import Users','bp-giu'); ?>" name="import_users">
			<?php wp_nonce_field('wplms_import_'.$user_id,'bpgiu_security'); ?>
		</form>	
		
		</div>
		<?php

		//check $_POST
		if(isset($_POST['import_users'])){

			if(!isset($_POST['bpgiu_security']) || !wp_verify_nonce($_POST['bpgiu_security'],'wplms_import_'.$user_id)){
				echo '<div class="error message">'.__('Security check Failed. Contact Administrator.','bp-giu' ).'</div>';
			}

			//call function to process csv
			$this->process_csv();

			echo '<div class="success message">'.__('Users Imported Successfully. Reload the page to check.','bp-giu').'</div>';
		}

	}

	function process_csv(){

		if(empty($_FILES))
			return;

		$file = $_FILES['users_csv']['tmp_name'];

		$labels = 0;
		$group_id = bp_get_group_id();
		if (($handle = fopen($file, "r")) !== FALSE) {
		    while ( ($data = fgetcsv($handle,1000,",") ) !== FALSE ) {
		    	if($labels){

		    		$email = $data[0];
		    		$mod = $data[1];
		    		$admin = $data[2];

		    		// Check if user already exists
        			$user_id = email_exists($email);
		    		if(!empty($user_id)){

		    			//Add user to group
		    			groups_join_group($group_id, $user_id );
		    			
		    			//Promote user to group moderator
		    			if($mod){
		    				groups_promote_member($user_id,$group_id,'mod');
		    			}
		    			//Promote user to group administrator
		    			if($admin){
		    				groups_promote_member($user_id,$group_id,'admin');
		    			}

		    		}else if(isset($_POST['create_user'])) {

		    			//Create new user
		    			$usermeta = array();
                		$usermeta['password'] = wp_hash_password( $email );
		    			$user_id = bp_core_signup_user( $email, $email, $email, $usermeta );

		    			//Add user to group
		    			groups_join_group($group_id, $user_id );
		    			
		    			//Promote user to group moderator
		    			if($mod){
		    				groups_promote_member($user_id,$group_id,'mod');
		    			}
		    			//Promote user to group administrator
		    			if($admin){
		    				groups_promote_member($user_id,$group_id,'admin');
		    			}
		    		}

		    	}else{ //Skips the first row/
		    		$labels = 1;
		    	}
		    }
		    fclose($handle);
		}
	}

} // End of class bp_group_import_users_class
