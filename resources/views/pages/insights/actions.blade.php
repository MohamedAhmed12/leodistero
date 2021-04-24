<div>
    <ul class="list-inline">
        <li class="list-inline-item align-top">
            <a href="{{ route('orders.show',$id) }}">
                <i class="fa fa-eye" aria-hidden="true" style="color: green"></i>
            </a>
        </li>
        <li class="list-inline-item align-top">
            <a href="{{ route('orders.edit',$id) }}">
                <i class="fas fa-edit"></i>
            </a>
        </li>
        <li class="list-inline-item align-top">
            <a onclick="event.preventDefault(); $('#delete_form_{{ $id }}').submit()" href="{{ route('orders.show',$id) }}">
                <i class="fa fa-trash" aria-hidden="true" style="color: red"></i>
            </a>
        </li>
    </ul>

    <form action="{{ route('orders.destroy',$id) }}" method="POST" id="delete_form_{{ $id }}">
        @csrf
        @method('DELETE')
    </form>
</div>
