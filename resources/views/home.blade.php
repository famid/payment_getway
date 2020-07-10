@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">User Page </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    Create Blog
                    <button type="button" class="btn btn-outline-success float-right">
                        <a href="{{route("createBlog")}}">create
                            <i class="fa fa-plus"></i>
                        </a>

                    </button>
                    <button type="button" class="btn btn-outline-success float-right">
                        <a href="{{route("viewImage")}}">View Image
                            <i class="fa fa-plus"></i>
                        </a>

                    </button>
                </div>

            </div>
        </div>
    </div>
    <div>
        <div class="card-header" style="background-color: #02d1f5"><h6 class="align-content-center">FUCK YOU MAMUN </h6>
        </div>
    </div>
</div>
@endsection
