<?php
    /* Tools Import */
    // show a preview of the import so the user can check their CSV file
    // before commiting to the import

$hdv_dashboard = get_option('hdv_dashboard');
$csvFile = "";
//if a CSV was uploaded
if (isset($_FILES["hdv_file_upload"])) {
    $upload_dir = wp_upload_dir();
    $hdv_upload_dir = $upload_dir['basedir'] .'/hdinvoice/';
    wp_mkdir_p($hdv_upload_dir);
    // check file type extention
    $hdv_extention = strtolower(pathinfo($_FILES['hdv_file_upload']['name'], PATHINFO_EXTENSION));
    if ($hdv_extention === "csv") {
        // also check mimetype since extention can be spoofed
        $hdv_mime = hdv_get_mime($_FILES['hdv_file_upload']['tmp_name']);
        if ($hdv_mime === "text/plain" || $hdv_mime === "text/csv") {
            if (!move_uploaded_file($_FILES['hdv_file_upload']['tmp_name'], $hdv_upload_dir.sanitize_text_field($_FILES['hdv_file_upload']['name']))) {
                die('Error uploading file - check destination is writeable.');
            }
            $csvFile = $hdv_upload_dir.sanitize_file_name($_FILES['hdv_file_upload']['name']);
        } else {
            die('Error uploading file - please only upload a .CSV file.');
        }
    } else {
        die('Error uploading file - please only upload a .CSV file.');
    }
}

if ($csvFile != "" && $csvFile != null) {
    $csvAsArray = array_map(function ($v) {
        return str_getcsv($v, ";", "@");
    }, file($csvFile));

    //this is the very first record, so let's make sure the fields match up
    if (isset($csvAsArray[1][0])) {
        $data0 = intVal($csvAsArray[1][0]);
    } else {
        $data0 = "";
    }
    if (isset($csvAsArray[1][1])) {
        $data1 = sanitize_text_field($csvAsArray[1][1]);
    } else {
        $data1 = "";
    }
    if (isset($csvAsArray[1][2])) {
        $data2 = floatVal($csvAsArray[1][2]);
    } else {
        $data2 = "";
    }
    if (isset($csvAsArray[1][3])) {
        $data3 = floatVal($csvAsArray[1][3]);
    } else {
        $data3 = "";
    }
    if (isset($csvAsArray[1][4])) {
        $data4 = floatVal($csvAsArray[1][4]);
    } else {
        $data4 = "";
    }
    if (isset($csvAsArray[1][5])) {
        $data5 = sanitize_text_field($csvAsArray[1][5]);
    } else {
        $data5 = "";
    }
    if (isset($csvAsArray[1][6])) {
        $data6 = sanitize_text_field($csvAsArray[1][6]);
    } else {
        $data6 = "";
    }
    if (isset($csvAsArray[1][7])) {
        $data7 = urldecode(wp_kses_data($csvAsArray[1][7]));
    } else {
        $data7 = "";
    }
    if (isset($csvAsArray[1][8])) {
        $data8 = urldecode(wp_kses_data($csvAsArray[1][8]));
    } else {
        $data8 = "";
    }

    echo '<div id ="hdv_check_before_import">';
    echo '<p>Before continuing, please ensure that the following fields appear accurate to you.</p>';
    echo '<table class = "hdv_table">';
    echo '<tr><td>Invoice #</td><td>'.$data0.'</td></tr>';
    echo '<tr><td>Customer Name</td><td>'.$data1.'</td></tr>';
    echo '<tr><td>Subtotal</td><td>'.$data2.'</td></tr>';
    echo '<tr><td>Total</td><td>'.$data3.'</td></tr>';
    echo '<tr><td>Amount Paid</td><td>'.$data4.'</td></tr>';
    echo '<tr><td>Publish Date</td><td>'.$data5.'</td></tr>';
    echo '<tr><td>Line Items</td><td>'.$data6.'</td></tr>';
    echo '<tr><td>Description</td><td>'.$data7.'</td></tr>';
    echo '<tr><td>Notes</td><td>'.$data8.'</td></tr>';
    echo '</table>';
    echo '<p style ="text-align:center;">If the above is correct, please continue the import</p>';
    echo '<div class="hd_button" id ="hdv_continue_import">CONTINUE IMPORT</div></div>';
    echo '<script>var hdv_csv_path = "'.$csvFile.'";</script>';
}

if ($csvFile == "" || $csvFile == null) {
    ?>

<div id = "hdv_tools_import">
	<h3>Please upload your CSV file and make sure it is properly formatted.</h3>
	<p>
		In order for the upload to work, your CSV must contain the following fields, in the following order. Required fields are in <strong>bold</strong>.
	</p>

	<div class = "one_third">

		<ol>
			<li>Invoice # <span class="hdv_tooltip">?<span class="hdv_tooltip_content"><span>Must be numerical only. If your Invoice ids are not numerical, then the default invoice IDs will be generated for you</span></span></span></li>
			<li><strong>Customer Name</strong></li>
			<li><strong>Subtotal</strong></li>
			<li><strong>Total</strong></li>
			<li><strong>Amount Paid</strong></li>
			<li>Publish Date <span class="hdv_tooltip">?<span class="hdv_tooltip_content"><span>Must be in date format "yyyy-mm-dd". Will default to today (<?php echo date("Y-m-d"); ?>) if not provided</span></span></span></li>
			<li>Line Items <span class="hdv_tooltip">?<span class="hdv_tooltip_content"><span>Must be in JSON format</span></span></span></li>
			<li>Description <span class="hdv_tooltip">?<span class="hdv_tooltip_content"><span>Public invoice description. <strong>Must be URL encoded</strong></span></span></span></li>
			<li>Notes <span class="hdv_tooltip">?<span class="hdv_tooltip_content"><span>Private hidden notes. <strong>Must be URL encoded</strong></span></span></span></li>
		</ol>
	</div>
	<div class = "two_third last">
		<form action="<?php echo the_permalink($hdv_dashboard); ?>?import=true" method="post" enctype="multipart/form-data">
			<div style = "margin: 0 auto; width: 100%; max-width: 600px">

				<p>Semicolon ";" should be used as a delimieter and the at "@" symbol should be used as the string delimiter. You can find more info on each field to the left/top.</p>
				<div class = "two_third">
					<input type="file" accept=".csv" name="hdv_file_upload" id="hdv_file_upload" required>
					<label for="hdv_file_upload"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path></svg> <span>Choose a file…</span></label>
				</div>
				<div class = "one_third last">
					<input type="submit" class="hd_button3" style = "padding: 0; height: 66px; width: 120px; top: -6px;" name="submit">
				</div>
				<div class = "clear"></div>
				<p>
					You will be able to see a preview once uploaded to confirm that the data matches before commiting to adding the invoices.
				</p>
			</div>

		</form>
	</div>
	<div class = "clear"></div>


</div>
<?php
} ?>
