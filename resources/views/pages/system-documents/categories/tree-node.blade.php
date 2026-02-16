<li>

    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border"
        style="margin-left: {{ $level * 20 }}px">

        <div class="flex items-center gap-2">

            @if ($category->children->count())
                <button type="button" class="toggle-btn text-primary font-bold"
                    data-target="children-{{ $category->id }}">
                    +
                </button>
            @else
                <span class="w-4"></span>
            @endif

            <span class="font-medium">
                {{ $category->translation?->name }}
            </span>

        </div>

        <div class="flex gap-2">
            @can('edit_document_categories')
                <a href="{{ route('document-categories.edit', $category->id) }}"
                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-success rounded-pill">
                    <i class="las la-edit"></i>
                </a>
            @endcan

            @can('delete_document_categories')
                <a href="javascript:void(0);"
                    onclick="showDeleteConfirmation('{{ __('are_you_sure') }}', {{ $category->id }})"
                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-danger rounded-pill">
                    <i class="las la-trash"></i>
                </a>
                <form id="delete-form-{{ $category->id }}" action="{{ route('document-categories.destroy', $category->id) }}"
                    method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endcan

        </div>

    </div>

    @if ($category->children->count())
        <ul id="children-{{ $category->id }}" class="hidden mt-2 space-y-2">
            @foreach ($category->children as $child)
                @include('pages.system-documents.categories.tree-node', [
                    'category' => $child,
                    'level' => $level + 1,
                ])
            @endforeach
        </ul>
    @endif

</li>
