<div class="item_group">

    <ul class="item-group-list">
        @foreach ($item_groups as $item_group)
            <li class="item-group-list-item">
                <a href="{{ route('home') }}" class="btn btn-default">
                    {{ $item_group->name }}
                </a>
            </li>
        @endforeach
    </ul>
    

</div>