
<div class="row children">
    @if($children->count() > 0)
    <span class="product-block-listname">СПИСОК ПОДКАТЕГОРИЙ ТОВАРОВ</span>
    <div class="product-block-buttons">
        @foreach($children as $child)
            @if($child->name == $subCategory)
                @php ($class = 'btn btn-model model-selected button_bot button_stat')
            @else
                @php ($class = 'btn btn-model button_bot button_stat')
            @endif
            <div class="col-md-4">
                <button type="button" class="{{ $class }}" value="{{ $child->name }}">
                    <span>{{ $child->name }}</span>
                </button>
            </div>
        @endforeach
    </div>
        @endif
</div>

<script>
    $('button.btn-model').on('click', function() {
        getProductsList(null, null, null, null, $(this).val(), <?php echo $parent_id; ?>);
    });
</script>
