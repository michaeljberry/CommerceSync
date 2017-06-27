<?php
include 'header-admin.php';
$statesToCollectTaxes = ["CA", "WA"];
?>
    <form id="amazon-csv" method="post" enctype="multipart/form-data">
        <input type="file" name="csvToUpload" id="csvToUpload">
        <select id="state" name="state">
            <?php
            foreach ($statesToCollectTaxes as $state) {
                echo "<option value='$state'>$state</option>";
            }
            ?>
        </select>
        <button class="submit" id="csv-upload">Upload Amazon Tax CSV</button>
    </form>
    <div id="csv-results">

    </div>
<?php
include 'footer-admin.php';
?>