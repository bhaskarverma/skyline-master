<?php

return '<div class="small-box {{box-type}}">
  <div class="inner">
    <h3>{{value}}</h3>

    <p>{{text}}</p>
  </div>
  <div class="icon">
    <i class="{{icon}}"></i>
  </div>
  <a data-toggle="modal" data-target="#sb-{{view-more-modal-id}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
</div>

<div id="sb-{{view-more-modal-id}}" class="modal pg-show-modal fade">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="breakdown_vehicle_title" class="modal-title">{{view-more-modal-title}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                {{view-more-modal-body}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>

<script>

$(function () {
  var table = $(\'#sb-{{view-more-modal-table}}\').DataTable({
    responsive: true,
    autoWidth: false,
    dom: \'lBfrtip\',
    buttons: [
      {
        text: \'Export to Excel\',
            extend: \'excel\',
            title: \'Skyline Report | {{name-of-report}}\'
      },
      {
        text: \'Export to PDF\',
        extend: \'pdf\',
        title: \'Skyline Report | {{name-of-report}}\'
      },
      {
        text: \'Print Page\',
        extend: \'print\',
        title: \'Skyline Report | {{name-of-report}}\'
      }
        ]
  });
});
</script>'

?>


