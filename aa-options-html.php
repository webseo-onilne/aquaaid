<?php 

/**
 * No direct access
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'No access' );
};

?>

<div class="wrap">

	<h1>Settings</h1>

	<form action="options.php" method="post">
		
		<?php $forms = GFAPI::get_forms(); ?>
		<?php settings_fields( 'aa-plugin-settings' ); ?>
		<?php do_settings_sections( 'aa-plugin-settings' ); ?>

		<table class="form-table">
			<tbody>

				<tr>
					<th><label for="g_select_1">Gravity Form 1:</label></th>
					<td><select id="g_select_1" name="g_select_1">
					<?php foreach ( $forms as $key => $value ) { ?>

						<option value="<?php echo $value['id'] ?>" 
							<?php echo esc_attr( get_option('g_select_1') ) == $value['id'] ? 'selected="selected"' : ''; ?>>
							<?php echo $value['title'] ?>
						</option>

					<?php } ?>
					</select></td>
				</tr>

				<tr>
					<th><label for="g_select_2">Gravity Form 2:</label></th>
					<td><select id="g_select_2" name="g_select_2">
					<?php foreach ( $forms as $key => $value ) { ?>

						<option value="<?php echo $value['id'] ?>" 
							<?php echo esc_attr( get_option('g_select_2') ) == $value['id'] ? 'selected="selected"' : ''; ?>>
							<?php echo $value['title'] ?>
						</option>

					<?php } ?>
					</select></td>
				</tr>

			</tbody>
		</table>	

		<?php submit_button(); ?>

	</form>

	<hr />

	<h1>Upload</h1>

	<form class="upform" enctype="multipart/form-data"> 
		<label for="aa_file_upload">Upload CSV</label>
		<input id="aa_file_upload" type="file" name="aa_file_upload"/>
		<input type="hidden" name="action" value="do_ajax_upload"/>
		<button class="upload button button-primary">Upload</button>
		<progress></progress>
	</form>

	<hr />

	<h1>View Data</h1>
	<div class="a-select-feild">
		<label for="a-select">Choose a location</lable>						
		<select id="a-select" class="area-select">
			<option value="null">-- Please Select --</option>
			<!-- Options loaded here -->
		</select>
	</div>	

	<table class="widefat fixed data-table" cellspacing="0">
    <thead>

    <tr>
			<th id="columnname" class="manage-column column-columnname" scope="col">Area</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Total Post Codes</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">Email</th>
			<th id="columnname" class="manage-column column-columnname" scope="col">CC</th>
			<th id="columnname" class="manage-column column-columnname" scope="col" width="400px">Message</th>
			<th id="columnname" class="manage-column column-columnname" scope="col"></th>			
    </tr>

    </thead>

    <tfoot>

    <tr>
			<th class="manage-column column-columnname" scope="col">Area</th>
			<th class="manage-column column-columnname" scope="col">Total Post Codes</th>
			<th class="manage-column column-columnname" scope="col">Email</th>
			<th class="manage-column column-columnname" scope="col">CC</th>
			<th class="manage-column column-columnname" scope="col" width="400px">Message</th>
			<th class="manage-column column-columnname" scope="col"></th>			
    </tr>

    </tfoot>

    <tbody><!-- Table contents loaded here --></tbody>

</table>



</div> 

<style>
.a-select-feild {
    margin-bottom: 20px;
}
</style>