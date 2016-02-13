<?php
define('perm', 'u_front_page_admin');
define('redirect', '../admin/index.php');
include("../partials/member_header.php");
?>
<script src="../js/sortable.min.js"></script>
<script src="../js/summernote.min.js"></script>
<script src="admin.js"></script>
<div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="addItemLabel"></h4>
      </div>
      <div class="modal-body" id="addItemBody">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success" id="addItemSaveButton"><span class="glyphicon glyphicon-plus"></span> Add</button>
      </div>
    </div>
  </div>
</div>

<section id="nav" class='content-section text-center'>
	<h2>Navigation</h2>
    <span id="nav-load" class="glyphicon glyphicon-refresh load"></span>
    <ul id="nav-accordion" class="panel-group" role="tablist" aria-multiselectable="true" style="display:none"></ul>
</section>

<section id="section" class='content-section text-center'>
	<h2>Sections</h2>
    <span id="section-load" class="glyphicon glyphicon-refresh load"></span>
    <ul id="section-accordion" class="panel-group" role="tablist" aria-multiselectable="true" style="display:none"></ul>
</section>
<?php
include("../partials/member_footer.php");
?>