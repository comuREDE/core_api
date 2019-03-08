<?php

require '_header.php';
if(isset($_GET['a']) && $_GET['a']==="logout"){
    logout();
}

?>


           

<?php
require '_footer.php';
