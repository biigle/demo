@if ($projects->isEmpty())
    @can ('create', Biigle\Project::class)
        <form role="form" method="POST" action="{{ url('api/v1/projects/demo') }}" style="margin-top: 5px;display:inline-block;">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <button type="submit" class="btn btn-success" title="Create a new demo project to explore BIIGLE features">Create Demo Project</button>
        </form>
    @else
        <button class="btn btn-success" title="Guests are not allowed to create a demo project" disabled>Create Demo Project</button>
    @endif
@endif
