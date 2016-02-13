<?php
define('perm', 'u_mission_list');
define('redirect', '../missions/index.php');
include("../partials/member_header.php");

$request->enable_super_globals();
include '/../config/config.php';

// Create connection
$connection = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_database, $mysql_serverport);

// Check connection
if ($connection->connect_error) {
    die("Problem connecting to the database");
}

$connection->autocommit(false);

$current_framework_version = 0;
$current_framework_version_query = <<<SQL
select max(id) as id from framework;
SQL;

$connection->begin_transaction();
if ($results = $connection->query($current_framework_version_query)) {
  while ($row = $results->fetch_assoc()) {
    $current_framework_version = $row['id'];
  }
}
else {
  $error = $connection->error;
  error_log("$error");
  exit;
}

$order = "order by m.created_on desc";
$having = "having count(mts.id) < 1";
switch ($_GET['filter']) {
  case 1:
    $having = "having count(mts.id) > 0";
    $order = "order by max(s.date) desc";
  break;
  case 2:
    $having = "having count(mts.id) > 0 and m.replayable = true";
    $order = "order by max(s.date) asc";
  break;
  case 3:
  break;
}

$missions_with_filter_query = <<<SQL
select m.id, m.name, map.friendly_name, count(mts.id) as play_count, max(s.date) as last_played,
ifnull(fw.version, 0) as framework_version, ifnull(fw.id, 0) as framework_id,
count(tmtm.id) as testers,
count(ti.id) as issues
from mission m
join map on m.map_id = map.id
left join mission_to_session mts on mts.mission_id = m.id
left join session s on mts.session_id = s.id
left join framework fw on m.framework_id = fw.id
left join tested_member_to_mission tmtm on tmtm.mission_id = m.id
left join testing_issues ti on ti.mission_id = m.id and severity in ('MAJOR','SEVERE') and fixed != true
group by m.id
$having
$order
SQL;

$missions = [];
if ($results = $connection->query($missions_with_filter_query)) {
  while ($row = $results->fetch_assoc()) {
    $mission = []; //this really should just be an object
    $mission['id'] = $row['id'];
    $mission['name'] = $row['name'];
    $mission['map'] = $row['friendly_name'];
    $mission['framework_version'] = $row['framework_version'];
    $mission['framework_id'] = $row['framework_id'];
    $mission['tested'] = $row['testers'] > 0;
    $mission['issues'] = $row['issues'] > 0;
    $missions[] = $mission;
  }
}
else {
  $error = $connection->error;
  error_log("$error");
  exit;
}
$connection->commit();

?>

<script src="missions.js"></script>
<div class="modal fade" id="confirm" tabindex="-1" role="dialog" aria-labelledby="confirmationLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="confirmationLabel">Are you sure?</h4>
      </div>
      <div class="modal-body" id="confirmationBody">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="confirmationNoButton" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> No</button>
        <button type="button" class="btn btn-danger" id="confirmationYesButton"><span class="glyphicon glyphicon-ok"></span> Yes</button>
      </div>
    </div>
  </div>
</div>

<section id="missions" class='content-section text-center'>
  <h2>Missions</h2>
  <ul id="mission-accordion" class="panel-group" role="tablist" aria-multiselectable="true">

    <?php
      foreach ($missions as $mission) {
        ?>
          <li data-id="<?php echo $mission['id']; ?>" data-name="<?php echo $mission['name']; ?>" id="mission-li-<?php echo $mission['id']; ?>" class="panel panel-default">
            <div id="mission-heading-<?php $mission['id'] ?>" class="panel-heading" role="tab">
              <h4 class="panel-title">
                <span class="mission-status">
                  <?php
                    $errorMessages = [];
                    $warnMessages = [];

                    if ($mission['issues']) {
                      $errorMessages[] = "Critical issues found during testing";
                    }
                    if ($mission['framework_id'] < $current_framework_version) {
                      $errorMessages[] = "Using old framework";
                    }
                    if (!$mission['tested']) {
                      $warnMessages[] = "Mission needs testing";
                    }
                    
                    if (count($errorMessages) > 0) {
                      if (count($warnMessages) > 0) {
                        $errorMessages = array_merge($errorMessages, $warnMessages);
                      }
                      $message = implode("<br>", $errorMessages);
                      echo "<span data-toggle='tooltip' data-html='true' data-placement='left' title='$message' class='glyphicon glyphicon-remove-sign' style='color: #DF0101;'></span>";
                    }
                    else if (count($warnMessages) > 0) {
                      $message = implode("<br>", $warnMessages);
                      echo "<span data-toggle='tooltip' data-html='true' data-placement='left' title='$message' class='glyphicon glyphicon-exclamation-sign' style='color: #FE9A2E;'></span>";
                    }
                    else {
                      echo "<span data-toggle='tooltip' data-html='true' data-placement='left' title='Mission ready to play' class='glyphicon glyphicon-ok-sign' style='color: #01DF3A;'></span>";
                    }
                    
                  ?>
                </span>
                <a data-toggle="collapse" data-parent="#mission-accordion" aria-expanded="false" aria-controls="mission-collapse-<?php echo $mission['id']; ?>" id="mission-header-<?php echo $mission['id']; ?>" class="collapsed" role="button" href="#mission-collapse-<?php echo $mission['id']; ?>"><?php echo $mission['name']; ?></a>
                <span class="map"><?php echo $mission['map']; ?></span>
              </h4>
            </div>
            <div aria-labelledby="mission-heading-<?php echo $mission['id']; ?>" id="mission-collapse-<?php echo $mission['id']; ?>" class="panel-collapse collapse" role="tabpanel" aria-expanded="false" style="height: 0px;">
              <div class="panel-body">
                <div class="input-group">
                  <span class="input-group-addon">Title</span>
                  <input aria-describedby="basic-addon1" id="mission-title-<?php echo $mission['id']; ?>" type="text" class="form-control" value="<?php echo $mission['name']; ?>">
                </div>
                <button id="mission-save-<?php echo $mission['id']; ?>" class="btn btn-success btn-left"><span class="glyphicon glyphicon-ok"></span> Save</button>
                <button id="mission-delete-<?php echo $mission['id']; ?>" class="btn btn-danger btn-right"><span class="glyphicon glyphicon-remove"></span> Delete</button>
              </div>
            </div>
          </li>
        <?php
      }
    ?>
  </ul>
</section>
<?php
include("../partials/member_footer.php");
?>