<?php

class ToDoList {

  private $card_start = '<div class="card">';

  private $card_end = '</div>';

  private $card_header = '<div class="card-header">
                      <h3 class="card-title">
                        <i class="ion ion-clipboard mr-1"></i>
                        Global To Do List
                      </h3>
                    </div>';

  private $card_body_start = '<div class="card-body">';

  private $card_body_end = '</div>';

  private $list_start = '<ul id="todo-list-global" class="todo-list" data-widget="todo-list">';

  private $list_end = '</ul>';

  private $list_item_raw = '<li>
                              <div class="icheck-primary d-inline ml-2">
                                <input onclick=markComplete(this) type="checkbox" value="" name="todo1" id="{{to-do-item-id}}">
                                <label for="{{to-do-item-id}}"></label>
                              </div>
                              <span class="text">{{to-do-text}}</span>
                              <small class="badge badge-{{badge-type}}"><i class="far fa-clock"></i> {{time-remaining}}</small>
                              <div class="tools">
                                <i class="fas fa-edit" onclick="updateTodo(\'{{to-do-item-id}}\', \'{{to-do-text}}\')"></i>
                                <i class="fas fa-trash-o"></i>
                              </div>
                            </li>';

  private $card_footer = '<div class="card-footer clearfix">
                            <button type="button" onclick="addTodo()" class="btn btn-info float-right"><i class="fas fa-plus"></i> Add item</button>
                          </div>';

  private $complete_html;
  private $item_id;
  private $text;
  private $badge;
  private $time_remaining;

  public function __construct()
  {
    $this->complete_html = $this->card_start.$this->card_header.$this->card_body_start.$this->list_start;
  }

  public function addItem($item_id, $text, $badge, $time_remaining)
  {
    $this->setValues($item_id, $text, $badge, $time_remaining);
    $this->generateItem();
  }

  private function setValues($item_id, $text, $badge, $time_remaining)
  {
    $this->item_id = $item_id;
    $this->text = $text;
    $this->badge = $badge;
    $this->time_remaining = $time_remaining;
  }

  private function generateItem()
  {
    $raw_html = $this->list_item_raw;
    $raw_html = str_replace("{{to-do-item-id}}", $this->item_id, $raw_html);
    $raw_html = str_replace("{{to-do-text}}", $this->text, $raw_html);
    $raw_html = str_replace("{{badge-type}}", $this->badge, $raw_html);
    $raw_html = str_replace("{{time-remaining}}", $this->time_remaining, $raw_html);
    $this->complete_html .= $raw_html;
  }

  public function getHTML()
  {
    return $this->complete_html.$this->list_end.$this->card_body_end.$this->card_footer.$this->card_end;
  }

}

?>

<script>

function addTodo()
{
    var swalHTML = "<label for='todo-text'>Todo Text</label><input id='todo-text' class='form-control' type='text' /><label for='end-dt'>Expected Date / Time to Finish</label><input id='end-dt' class='form-control' type='datetime-local' />";
    Swal.fire({
      title: 'Add a TODO',
      html: swalHTML,
      inputAttributes: {
        autocapitalize: 'off'
      },
      showCancelButton: true,
      confirmButtonText: 'Add',
      showLoaderOnConfirm: true,
      preConfirm: function() {
            return new Promise((resolve, reject) => {
                // get your inputs using their placeholder or maybe add IDs to them
                resolve({
                    todo: $('#todo-text').val(),
                    end_dt: $('#end-dt').val()
                });

                // maybe also reject() on some condition
            });
      },
      allowOutsideClick: () => !Swal.isLoading()
    }).then((data) => {
        return fetch('/modules/core/to_do_list/add_todo.php', {
          method: 'post',
          headers: {
            "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
          },
          body: 'todo='+data.value.todo+'&end_dt='+data.value.end_dt
        }).then(response => {
          if(!response.ok)
          {
            throw new Error(response.statusText)
          }
          return response.json()
        })
        .catch(error => {
          Swal.showValidationMessage(
              `Request Failed: ${error}`
            )
        })
    }).then((result) => {
      Swal.fire(
          'Request Completed',
          result.res,
          'success'
      )
    });

}

function updateTodo(id, val_td, val_t)
{
  var swalHTML = "<label for='todo-text'>Todo Text</label><input id='todo-text-update' class='form-control' type='text' value='"+val_td+"' disabled='disabled' /><label for='end-dt'>Expected Date / Time to Finish</label><input value='"+val_t+"' id='end-dt-update' class='form-control' type='datetime-local' /><input id='td_id' type='hidden' value='"+id+"' />";
    Swal.fire({
      title: 'Update a TODO',
      html: swalHTML,
      inputAttributes: {
        autocapitalize: 'off'
      },
      showCancelButton: true,
      confirmButtonText: 'Update',
      showLoaderOnConfirm: true,
      preConfirm: function() {
            return new Promise((resolve, reject) => {
                // get your inputs using their placeholder or maybe add IDs to them
                resolve({
                    td_id: $('#td_id').val(),
                    todo: $('#todo-text-update').val(),
                    end_dt: $('#end-dt-update').val()
                });

                // maybe also reject() on some condition
            });
      },
      allowOutsideClick: () => !Swal.isLoading()
    })
    .then((data) => {
        return fetch('/modules/core/to_do_list/update_todo.php', {
          method: 'post',
          headers: {
            "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
          },
          body: 'todo='+data.value.todo+'&end_dt='+data.value.end_dt+'&td_id='+data.value.td_id
        }).then(response => {
          if(!response.ok)
          {
            throw new Error(response.statusText)
          }
          return response.json()
        })
        .catch(error => {
          Swal.showValidationMessage(
              `Request Failed: ${error}`
            )
        })
    })
    .then((result) => {
      Swal.fire(
          'Request Completed',
           result.res,
          'success'
      )
    });
}

function markComplete(checkbox)
{
  swal.fire(checkbox.id);
}

</script>