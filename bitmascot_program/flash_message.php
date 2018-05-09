<?php
	function set_flash_message($key, $value, $type='warning'){
		$_SESSION['flash'][$key]= '<div class="'.$type.'">'.$value.'</div>';
	}

	function get_flash_message(){
		if(isset($_SESSION['flash']) && sizeof($_SESSION['flash'])>0){
			echo '<div class="message">';
				foreach($_SESSION['flash'] as $key=>$value){
					echo $value;
				}
			echo '</div>';
			unset($_SESSION['flash']);
		}
	}

	set_flash_message('require_reference', 'Please Enter reference code.');
	set_flash_message('test_key', 'test value');
?>

<script type="text/javascript">
	window.location = ''; // redirect url here 
</script>
