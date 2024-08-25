@extends('layouts.app')

@section('content')

<div class="container py-5">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">

            <div id="success_message"></div>

            <div class="card">
                <div class="card-header d-flex justify-content-center align-items-center mt-3">
                    <input type="text" name="task" class="form-control me-4 ms-4" style="width: 200px;">
                    <button type="button" class="btn btn-primary add_task">Add Task</button>
                  &nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-info show-task">Show Task</button>
                </div>

             <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>#ID</th>
                                <th>Task</th>
                                <th>Status</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-3"></div>
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="DeleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete Todo Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>Are u sure to delete this task ?</h4>
                <input type="hidden" id="deleteing_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary deleteTodo">Yes Delete</button>
            </div>
        </div>
    </div>
</div>
{{-- End - Delete Modal --}}

{{-- Error Model --}}

<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="errorModalLabel">Error</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul id="errorMessages" class="list-unstyled"></ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
{{-- end error model --}}

{{-- show all task Modal --}}
<div class="modal fade" id="allTasksModal" tabindex="-1" aria-labelledby="allTasksModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="allTasksModalLabel">All Tasks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul id="tasksList" class="list-group">

                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



{{-- end all task model --}}

@endsection

@section('scripts')

<script>
    $(document).ready(function () {
     ToDolist();
     function ToDolist() {
    $.ajax({
        type: "GET",
        url: "/toDolist",
        dataType: "json",
        success: function (response) {
           // console.log(response);
            $('tbody').html("");
            let index = 1;
            $.each(response.todos, function (key, item) {
                $('tbody').append('<tr id="task-row-' + item.id + '">\
                <td><input type="checkbox" class="status-checkbox" value="' + item.id + '"' + (item.status === 2 ? 'checked' : '') + '></td>\
                <td>' + index + '</td> \
                <td>' + item.task + '</td>\
                <td>' + (item.status === 1 ? 'Pending' : 'Completed') + '</td>\
                <td><button type="button" value="' + item.id + '" class="btn btn-danger deletebtn btn-sm">Delete</button></td>\
            </tr>');
            index++;
        });

        }
    });
}

// ---all todo list--------------------------



// ----------add task--------------------------------------//

        $(document).on('click', '.add_task', function (e) {
            e.preventDefault();

            $(this).text('Submit..');

            var data = {
                'task': $('input[name="task"]').val(),
                'status': 1 // Default status
               }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "/submit/toDo",
                data: data,
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    if (response.status == 400) {
                        $('#errorMessages').html('');
                        $.each(response.errors, function (key, err_value) {
                            $('#errorMessages').append('<li>' + err_value + '</li>');
                        });
                        $('#errorModal').modal('show');
                        $('.add_task').text('Add Task');
                    } else {
                        $('#success_message').html('');
                        $('#success_message').addClass('alert alert-success');
                        $('#success_message').text(response.message);

                        // Reset the text box
                        $('input[name="task"]').val('');

                        $('.add_task').text('Add Task');
                        ToDolist();
                    }
                }
            });


        });

   //------------end--------------------------------------//

//--------------show all task -------------//

$(document).on('click', '.show-task', function (e) {
    e.preventDefault();

    $.ajax({
        type: "GET",
        url: "/alltoDolist",
        dataType: "json",
        success: function (response) {
            $('#tasksList').html(''); // Clear any existing tasks
            $.each(response.todos, function (key, item) {
                $('#tasksList').append('<li class="list-group-item">\
                    <strong>ID:</strong> ' + item.id + '<br>\
                    <strong>Task:</strong> ' + item.task + '<br>\
                    <strong>Status:</strong> ' + (item.status === 1 ? 'Pending' : 'Completed') + '\
                </li>');
            });
            $('#allTasksModal').modal('show'); // Show the modal
        }
    });
});


//----------------end-----------------------//

   //---------------delete task----------------------------------//
        $(document).on('click', '.deletebtn', function () {
            var id = $(this).val();
            $('#DeleteModal').modal('show');
            $('#deleteing_id').val(id);
        });

        $(document).on('click', '.deleteTodo', function (e) {
            e.preventDefault();

            $(this).text('Deleting..');
            var id = $('#deleteing_id').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "DELETE",
                url: "/delete/toDo/" + id,
                dataType: "json",
                success: function (response) {
                    // console.log(response);
                    if (response.status == 404) {
                        $('#success_message').addClass('alert alert-success');
                        $('#success_message').text(response.message);
                        $('.deleteTodo').text('Yes Delete');
                    } else {
                        $('#success_message').html("");
                        $('#success_message').addClass('alert alert-success');
                        $('#success_message').text(response.message);
                        $('.deleteTodo').text('Yes Delete');
                        $('#DeleteModal').modal('hide');
                        ToDolist();
                    }
                }
            });
        });

     //--------------end delete-------------------------//

    });


 // --update status---------------//

 $(document).on('change', '.status-checkbox', function () {
    var taskId = $(this).val();
    var isChecked = $(this).is(':checked');

    var data = {
        'id': taskId,
        'status': isChecked ? 2 : 1 // 2 for Completed, 1 for Pending
    };

    $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
    $.ajax({
        type: "POST",
        url: "/updateTaskStatus", // Update with your correct route
        data: data,
        dataType: "json",
        success: function (response) {
            if(response.status === 200) {
                // If status is changed, remove the row
                if(isChecked) {
                    $('#task-row-' + taskId).fadeOut('slow', function() {
                        $(this).remove();
                    });
                }
            }
        }
    });
});



</script>

@endsection
