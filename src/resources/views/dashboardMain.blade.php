@if ($projects->isEmpty())
<div class="row">
    <div class="col-xs-12 dashboard__all-projects">
        <form role="form" method="POST" action="{{ url('api/v1/projects/demo') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <button type="submit" class="btn btn-success btn-lg" title="Create a new demo project so you can explore BIIGLE features">
                <i class="fa fa-hand-o-right"></i> Create Demo Project
            </button>
        </form>
    </div>
</div>
@endif
