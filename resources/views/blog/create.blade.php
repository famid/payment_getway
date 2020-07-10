@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card" style="background-color: #e9ecee">
                    <div class="card-header"style="background-color: beige"> <span>Create Your Blog</span></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="custom-input-label" for="title">Title:</label>
                            <input type="text" id="title" class="form-control custom-input-field" name="title" value="">
                        </div>
                        <div class="from-group pt-2">
                            <label class="custom-input-label" for="description">Description :</label>
                            <textarea  type="text" id="description" class="form-control custom-input-field"></textarea>
                        </div>
                        <div class="form-group pt-5">
                            <label class="custom-input-label" for="tags">Select Your Tag :</label>
                            <select name="tags" class="form-control mt-2 custom-input-field" id="tags">
                                <option value="science">Science</option>
                                <option value="romantic">Romantic</option>
                                <option value="thriller">Thriller</option>
                            </select>
                        </div>
                        <div class="form-group pt-4">
                            <button id="submit" class="btn btn-outline-success">Save</button>
                        </div>
                    </div>
{{--===============================================================--}}
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table" id="blogList" width="100%">
                                        <thead>
                                        <tr>
                                            <th class="all">{{__('Title')}}</th>
                                            <th class="all">{{__('Description')}}</th>
                                            <th class="all">{{__('Tags')}}</th>
                                            <th class="all">{{__('Created_At')}}</th>
                                            <th class="all">{{__('Updated_At')}}</th>
                                            <th class="desktop">{{__('Actions')}}</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
{{--===============================================================--}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function () {
            $('#blogList').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                pageLength: 2,
                ajax: '{{route('getAllBlog')}}',
                order: [0, 'asc'],
                autoWidth:false,
                columnDefs: [
                    {"className": "text-center", "targets": "_all"}
                ],
                columns: [
                    {"data": "title"},
                    {"data": "description"},
                    {"data": "tags"},
                    {"data": "created_at"},
                    {"data": "updated_at"},
                    {"data": "action", orderable: false, searchable: false}
                ]
            });

            $("#submit").on('click', function(){

                let title = $("#title").val();
                let description = $("#description").val();
                let tags = $("#tags").val();
                saveBlog(title,description,tags);
                resetInputFields();
            });

        });
        //Methods
        function saveBlog(title,description,tags) {
            $.ajax({
                url: '{{route('createBlog')}}',
                method: 'POST',
                data:{
                    '_token': '{{csrf_token()}}',
                    'title':title,
                    'description':description,
                    'tags':tags
                }
            }).done(function (data) {
                console.log(data);
            }).fail(function (error) {
                console.log(error);
            });
        }

        function resetInputFields() {
            $("#title , #description, #tags").val('');
        }
    </script>
@endsection
