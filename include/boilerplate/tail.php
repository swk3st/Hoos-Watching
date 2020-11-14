<?php

/**
 * CS4750
 * Hoo's Watching
 * Jessica Heavner (jlh9qv), Julian Cornejo Castro (jac9vn), Patrick Thomas (pwt5ca), & Solimar Kwa (swk3st)
 */

$sql = "CALL num_new_and_old_users(@p0);";

global $db;

$statement = $db->prepare($sql);
if (!$statement) {
    return false;
}
$statement->execute();

$users_7_day = null;
$users_30_day = null;
$tot_users = null;
$statement->bind_result($users_7_day, $users_30_day, $tot_users);
$statement->fetch();
$statement->close();

?>

<footer class="footer mt-5 pt-3">
    <div class="container">
        <p class="text-muted">CS 4750. By Jessica Heavner (jlh9qv), Julian Cornejo Castro (jac9vn), Patrick Thomas (pwt5ca), & Solimar Kwa (swk3st). <?php echo $users_7_day; ?> new users in the past 7 days, <?php echo $users_30_day; ?> new users in the past 30 days, and <?php echo $tot_users; ?> users total. </p>
    </div>
</footer>
</body>

</html>